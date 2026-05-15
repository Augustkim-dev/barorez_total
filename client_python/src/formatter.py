"""ESC/POS 바이트 시퀀스 생성.

PRD §7.2 payload 스펙에 맞춰 print_job 메시지를 바이트로 변환한다.
페이로드 형식:
{
  "table_name": "3번",
  "order_time": "2026-05-12 14:23:01",
  "items": [
    {"name": "...", "qty": 1, "options": ["맵게"], "unit_price": 8000}
  ],
  "memo": "...",       # optional
  "summary": {"total": 20000}  # optional — 없으면 items 합산
}

가격 필드(`unit_price`, `summary.total`)는 모두 선택. 없으면 메뉴 라인에서
수량만 표시하고 합계 라인 생략 — 기존 페이로드와 100% 하위 호환.
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
DOUBLE_HEIGHT = GS + b"!" + bytes([0x01])  # 세로 2배 (width 1x)
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

# 본문 폰트 크기 옵션
BODY_SIZE_MAP = {
    "normal": (NORMAL_SIZE, 1),
    "double_height": (DOUBLE_HEIGHT, 1),  # 폭은 그대로, 세로만 2배
    "double_wh": (DOUBLE_WH, 2),  # 가로·세로 모두 2배 — 유효 폭 절반
}

# 컬럼 폭 (가격 없는 페이로드용 — 메뉴명 + 수량만)
_QTY_ONLY_COL_W = 3  # "999"

# 4컬럼 표 (가격 있는 페이로드용) — 메뉴명/단가/수량/금액
# "15,000원" = 6 + 2 ('원') = 8폭, "99개" = 2 + 2 = 4폭, "999,000원" = 9폭
_UNIT_PRICE_COL_W = 8
_PRICED_QTY_COL_W = 4
_LINE_TOTAL_COL_W = 9
_COL_GAP = 1

_HEADER_NAME = "메뉴명"
_HEADER_UNIT = "단가"
_HEADER_QTY = "수량"
_HEADER_TOTAL = "금액"
_TOTAL_LABEL = "합계"


def _display_width(s: str) -> int:
    """한글·CJK 는 2폭, 그 외는 1폭으로 계산."""
    w = 0
    for ch in s:
        cp = ord(ch)
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


def _pad_left(s: str, width: int) -> str:
    pad = width - _display_width(s)
    return (" " * pad if pad > 0 else "") + s


def _separator(width: int) -> str:
    return "-" * width


def _truncate(s: str, width: int) -> str:
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


def _money(n: int | float) -> str:
    try:
        return f"{int(n):,}"
    except (TypeError, ValueError):
        return ""


def _resolve_prices(items: Iterable[dict[str, Any]]) -> tuple[bool, int]:
    """items 중 unit_price 가 있는 항목이 하나라도 있으면 가격 컬럼 활성화.
    반환: (has_price, computed_total)
    """
    has_price = False
    total = 0
    for item in items:
        up = item.get("unit_price")
        if up is None:
            up = item.get("price")
        if up is None:
            continue
        try:
            up_int = int(up)
        except (TypeError, ValueError):
            continue
        if up_int > 0:
            has_price = True
            qty = int(item.get("qty") or 0)
            total += up_int * qty
    return has_price, total


def build(
    payload: dict[str, Any],
    *,
    printer_type: str,
    job_id: int | None,
    codepage: str = "cp949",
    width: int = 48,
    escpos_codepage_id: int | None = None,
    right_margin: int = 2,
    body_font_size: str = "double_height",
) -> bytes:
    """페이로드 dict 를 ESC/POS 바이트열로 변환.

    body_font_size:
      - 'normal'        — 본문 표준 크기
      - 'double_height' — 본문 세로 2배 (폭 동일, 권장 기본값)
      - 'double_wh'     — 본문 가로·세로 2배 (유효 폭 절반)
    """
    body_cmd, width_divisor = BODY_SIZE_MAP.get(body_font_size, BODY_SIZE_MAP["double_height"])
    effective_width = max(1, (width - max(0, right_margin)) // width_divisor)

    buf = bytearray()
    buf += INIT

    if escpos_codepage_id is not None:
        buf += ESC + b"t" + bytes([escpos_codepage_id & 0xFF])

    # 헤더 — 가운데, 가로·세로 2배 (본문 크기와 별개, 항상 큼)
    label = PRINTER_TYPE_LABEL.get(printer_type, printer_type)
    buf += ALIGN_CENTER
    buf += DOUBLE_WH
    buf += _encode(label, codepage)
    buf += LF
    buf += NORMAL_SIZE
    buf += LF

    # 본문 시작 — body_font_size 적용
    buf += body_cmd

    # 테이블 + 시각 (가운데)
    table = str(payload.get("table_name") or "-")
    order_time = str(payload.get("order_time") or "")
    buf += _encode(f"테이블 {table}", codepage)
    buf += LF
    if order_time:
        buf += _encode(order_time, codepage)
        buf += LF

    # 본문 정렬 좌측
    buf += ALIGN_LEFT
    buf += _encode(_separator(effective_width), codepage)
    buf += LF

    items: list[dict[str, Any]] = list(payload.get("items") or [])
    has_price, computed_total = _resolve_prices(items)

    # 가격 있는 페이로드 — 컬럼 헤더 라인 추가 (구분선 직전에 이미 분리 라인 있음)
    if has_price:
        name_col = effective_width - _UNIT_PRICE_COL_W - _PRICED_QTY_COL_W - _LINE_TOTAL_COL_W - (_COL_GAP * 3)
        priced_layout_ok = name_col >= 6  # 메뉴명 최소 6폭 (한글 3자)
    else:
        priced_layout_ok = False
        name_col = 0  # not used

    # 컬럼 헤더 (가격 있을 때만)
    if priced_layout_ok:
        # 메뉴명 좌측 + 단가/수량/금액 우측 정렬
        # 메뉴 라인의 컬럼 위치와 정확히 일치하도록 동일 폭 사용
        header_line = (
            _pad_right(_HEADER_NAME, name_col)
            + (" " * _COL_GAP)
            + _pad_left(_HEADER_UNIT, _UNIT_PRICE_COL_W)
            + (" " * _COL_GAP)
            + _pad_left(_HEADER_QTY, _PRICED_QTY_COL_W)
            + (" " * _COL_GAP)
            + _pad_left(_HEADER_TOTAL, _LINE_TOTAL_COL_W)
        )
        # 구분선 직전에 헤더 한 줄 — 위 코드에서 이미 구분선이 찍혀 있어 그것을
        # 살리고 헤더는 그 다음에 둠. 헤더 + 새 구분선 1줄 추가.
        buf += _encode(header_line, codepage)
        buf += LF
        buf += _encode(_separator(effective_width), codepage)
        buf += LF

    # 메뉴 라인
    for idx, item in enumerate(items):
        name = str(item.get("name") or "")
        qty = int(item.get("qty") or 0)

        if priced_layout_ok:
            up = item.get("unit_price") if item.get("unit_price") is not None else item.get("price")
            try:
                up_int = int(up) if up is not None else 0
            except (TypeError, ValueError):
                up_int = 0
            unit_str = (_money(up_int) + "원") if up_int > 0 else ""
            qty_str = f"{qty}개"
            line_total_str = (_money(up_int * qty) + "원") if up_int > 0 else ""

            line = (
                _pad_right(_truncate(name, name_col), name_col)
                + (" " * _COL_GAP)
                + _pad_left(unit_str, _UNIT_PRICE_COL_W)
                + (" " * _COL_GAP)
                + _pad_left(qty_str, _PRICED_QTY_COL_W)
                + (" " * _COL_GAP)
                + _pad_left(line_total_str, _LINE_TOTAL_COL_W)
            )
        elif has_price:
            # 가격 있으나 폭이 부족 — 메뉴명 + 수량만 (fallback)
            fb_col = effective_width - _QTY_ONLY_COL_W - _COL_GAP
            line = (
                _pad_right(_truncate(name, fb_col), fb_col)
                + (" " * _COL_GAP)
                + _pad_left(str(qty), _QTY_ONLY_COL_W)
            )
        else:
            # 가격 없음 — 기존 형식 (메뉴명 + 수량)
            fb_col = effective_width - _QTY_ONLY_COL_W - _COL_GAP
            line = (
                _pad_right(_truncate(name, fb_col), fb_col)
                + (" " * _COL_GAP)
                + _pad_left(str(qty), _QTY_ONLY_COL_W)
            )

        buf += BOLD_ON
        buf += _encode(line, codepage)
        buf += BOLD_OFF
        buf += LF

        # 옵션 — 들여쓰기. PHP 새 형식(string) 우선, 옛 형식(dict) 백워드 호환.
        for opt in item.get("options") or []:
            if isinstance(opt, dict):
                opt_name = str(opt.get("option_name") or "").strip()
                try:
                    opt_price = int(opt.get("option_price") or 0)
                except (TypeError, ValueError):
                    opt_price = 0
                if not opt_name:
                    continue
                opt_text = f"{opt_name} +{opt_price:,}원" if opt_price > 0 else opt_name
            else:
                opt_text = str(opt).strip()
                if not opt_text:
                    continue
            opt_line = "  - " + _truncate(opt_text, effective_width - 4)
            buf += _encode(opt_line, codepage)
            buf += LF

        # 메뉴 사이 줄간격 — 마지막 메뉴 뒤에는 생략 (어차피 다음 구분선과 붙음)
        if idx < len(items) - 1:
            buf += LF

    # 합계 (가격이 있을 때만)
    summary = payload.get("summary") if isinstance(payload.get("summary"), dict) else {}
    total: int | None = None
    if summary and "total" in summary:
        try:
            total = int(summary["total"])
        except (TypeError, ValueError):
            total = None
    if total is None and has_price:
        total = computed_total
    if total is not None and total > 0:
        buf += _encode(_separator(effective_width), codepage)
        buf += LF
        total_str = _money(total) + "원"
        gap = effective_width - _display_width(_TOTAL_LABEL) - _display_width(total_str)
        gap = max(1, gap)
        buf += BOLD_ON
        buf += _encode(_TOTAL_LABEL + (" " * gap) + total_str, codepage)
        buf += BOLD_OFF
        buf += LF

    # 메모
    memo_raw = payload.get("memo")
    memo = memo_raw.strip() if isinstance(memo_raw, str) else ""
    if memo:
        buf += _encode(_separator(effective_width), codepage)
        buf += LF
        buf += BOLD_ON
        buf += _encode("[메모]", codepage)
        buf += BOLD_OFF
        buf += LF
        line = ""
        line_w = 0
        for ch in memo:
            ch_w = _display_width(ch)
            if line_w + ch_w > effective_width:
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
    buf += _encode(_separator(effective_width), codepage)
    buf += LF
    if job_id is not None:
        buf += ALIGN_CENTER
        buf += _encode(f"job #{job_id}", codepage)
        buf += LF
    buf += ALIGN_LEFT

    # 본문 크기 복원 + 여백 + cut
    buf += NORMAL_SIZE
    buf += LF + LF + LF
    buf += CUT_PARTIAL

    return bytes(buf)
