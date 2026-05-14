"""ESC/POS 바이트 시퀀스 생성.

PRD §7.2 payload 스펙에 맞춰 print_job 메시지를 바이트로 변환한다.
페이로드 형식:
{
  "table_name": "3번",
  "order_time": "2026-05-12 14:23:01",
  "items": [{"name": "...", "qty": 1, "options": ["맵게"]}],
  "memo": "..."  # optional
}
"""

from __future__ import annotations

from typing import Any, Iterable

from .errors import UnsupportedEncoding

# ESC/POS 제어 코드
ESC = b"\x1b"
GS = b"\x1d"
LF = b"\x0a"

INIT = ESC + b"@"
ALIGN_LEFT = ESC + b"a" + bytes([0])
ALIGN_CENTER = ESC + b"a" + bytes([1])
DOUBLE_HEIGHT = GS + b"!" + bytes([0x01])  # 세로 2배
DOUBLE_WH = GS + b"!" + bytes([0x11])  # 가로·세로 2배
NORMAL_SIZE = GS + b"!" + bytes([0x00])
BOLD_ON = ESC + b"E" + bytes([1])
BOLD_OFF = ESC + b"E" + bytes([0])
CUT_PARTIAL = GS + b"V" + bytes([66, 0])  # feed + partial cut

PRINTER_TYPE_LABEL = {
    "kitchen": "주방",
    "counter": "카운터",
    "bar": "바",
}


def _display_width(s: str) -> int:
    """한글·CJK 는 2폭, 그 외는 1폭으로 계산. wcwidth 외부 의존 회피."""
    w = 0
    for ch in s:
        cp = ord(ch)
        # 한글 음절 + 한글 자모 + CJK 통합 한자 + 전각 기호
        if (
            0x1100 <= cp <= 0x115F
            or 0x2E80 <= cp <= 0x303E
            or 0x3041 <= cp <= 0x33FF
            or 0x3400 <= cp <= 0x4DBF
            or 0x4E00 <= cp <= 0x9FFF
            or 0xA000 <= cp <= 0xA4CF
            or 0xAC00 <= cp <= 0xD7A3
            or 0xF900 <= cp <= 0xFAFF
            or 0xFE30 <= cp <= 0xFE4F
            or 0xFF00 <= cp <= 0xFF60
            or 0xFFE0 <= cp <= 0xFFE6
        ):
            w += 2
        else:
            w += 1
    return w


def _pad_right(s: str, width: int) -> str:
    pad = width - _display_width(s)
    return s + (" " * pad if pad > 0 else "")


def _separator(width: int) -> str:
    return "-" * width


def _truncate(s: str, width: int) -> str:
    """폭 초과 시 폭 기준으로 자른다."""
    if _display_width(s) <= width:
        return s
    out = []
    w = 0
    for ch in s:
        ch_w = 2 if _display_width(ch) == 2 else 1
        if w + ch_w > width:
            break
        out.append(ch)
        w += ch_w
    return "".join(out)


def _encode(text: str, codepage: str) -> bytes:
    try:
        return text.encode(codepage, errors="strict")
    except (UnicodeEncodeError, LookupError) as e:
        raise UnsupportedEncoding(
            f"cannot encode text in {codepage}: {e}"
        ) from e


def build(
    payload: dict[str, Any],
    *,
    printer_type: str,
    job_id: int | None,
    codepage: str = "cp949",
    width: int = 48,
    escpos_codepage_id: int | None = None,
) -> bytes:
    """페이로드 dict 를 ESC/POS 바이트열로 변환."""
    buf = bytearray()
    buf += INIT

    # 펌웨어 측 코드페이지 선택 (옵션)
    if escpos_codepage_id is not None:
        buf += ESC + b"t" + bytes([escpos_codepage_id & 0xFF])

    # 헤더 — 출력 종류 (가운데, 가로·세로 2배)
    label = PRINTER_TYPE_LABEL.get(printer_type, printer_type)
    buf += ALIGN_CENTER
    buf += DOUBLE_WH
    buf += _encode(label, codepage)
    buf += LF
    buf += NORMAL_SIZE
    buf += LF

    # 테이블 + 시각 (가운데)
    table = str(payload.get("table_name") or "-")
    order_time = str(payload.get("order_time") or "")
    buf += _encode(f"테이블 {table}", codepage)
    buf += LF
    if order_time:
        buf += _encode(order_time, codepage)
        buf += LF

    # 구분선
    buf += ALIGN_LEFT
    buf += _encode(_separator(width), codepage)
    buf += LF

    # 메뉴 라인 (좌측 정렬)
    items: Iterable[dict[str, Any]] = payload.get("items") or []
    for item in items:
        name = str(item.get("name") or "")
        qty = int(item.get("qty") or 0)
        qty_str = f" x{qty}"
        name_col = width - _display_width(qty_str)
        # 메뉴명은 진하게
        buf += BOLD_ON
        line = _pad_right(_truncate(name, name_col), name_col) + qty_str
        buf += _encode(line, codepage)
        buf += BOLD_OFF
        buf += LF
        # 옵션 들여쓰기
        for opt in item.get("options") or []:
            opt_line = "  - " + _truncate(str(opt), width - 4)
            buf += _encode(opt_line, codepage)
            buf += LF

    # 메모
    memo = (payload.get("memo") or "").strip() if isinstance(payload.get("memo"), str) else ""
    if memo:
        buf += _encode(_separator(width), codepage)
        buf += LF
        buf += BOLD_ON
        buf += _encode("[메모]", codepage)
        buf += BOLD_OFF
        buf += LF
        # 메모는 폭 단위로 줄바꿈
        line = ""
        line_w = 0
        for ch in memo:
            ch_w = _display_width(ch)
            if line_w + ch_w > width:
                buf += _encode(line, codepage)
                buf += LF
                line = ch
                line_w = ch_w
            else:
                line += ch
                line_w += ch_w
        if line:
            buf += _encode(line, codepage)
            buf += LF

    # 푸터
    buf += _encode(_separator(width), codepage)
    buf += LF
    if job_id is not None:
        buf += ALIGN_CENTER
        buf += _encode(f"job #{job_id}", codepage)
        buf += LF
    buf += ALIGN_LEFT
    # 마지막 여백
    buf += LF + LF + LF

    # 컷
    buf += CUT_PARTIAL

    return bytes(buf)
