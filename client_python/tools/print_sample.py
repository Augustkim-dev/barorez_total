"""가짜 페이로드 JSON → ESC/POS 변환 → 실 프린터 출력.

사용:
    python -m client_python.tools.print_sample sample_payloads/kitchen_basic.json
    python -m client_python.tools.print_sample sample_payloads/kitchen_basic.json --dump out.bin
    python -m client_python.tools.print_sample sample_payloads/kitchen_basic.json --config ../config.ini

JSON 스키마 (top-level):
{
  "printer_type": "kitchen" | "counter" | "bar",
  "job_id": 12345,  // optional
  "payload": { ... }  // PRD §7.2 payload
}

--dump 옵션 사용 시 실 프린터 출력 없이 RAW 바이트만 파일로 저장 (드라이버
설치 전 단계 검증에 사용).
"""

from __future__ import annotations

import argparse
import json
import sys
from pathlib import Path


def _resolve_imports():
    """패키지 / 직접 실행 양쪽에서 동작하도록 임포트 보조."""
    try:
        from ..src import config, formatter, printer
        from ..src.errors import PrintError
        return config, formatter, printer, PrintError
    except (ImportError, ValueError):
        sys.path.insert(0, str(Path(__file__).resolve().parents[2]))
        from client_python.src import config, formatter, printer  # type: ignore[no-redef]
        from client_python.src.errors import PrintError  # type: ignore[no-redef]
        return config, formatter, printer, PrintError


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description="Phase 3 D012 sample print")
    parser.add_argument("payload_json", help="페이로드 JSON 파일 경로")
    parser.add_argument(
        "--config",
        default=None,
        help="config.ini 경로 (기본: client_python/config.ini)",
    )
    parser.add_argument(
        "--dump",
        default=None,
        help="실 프린터 대신 RAW bytes 를 파일에 저장",
    )
    args = parser.parse_args(argv)

    config_mod, formatter_mod, printer_mod, PrintError = _resolve_imports()

    payload_path = Path(args.payload_json)
    if not payload_path.exists():
        print(f"payload not found: {payload_path}", file=sys.stderr)
        return 2

    with payload_path.open("r", encoding="utf-8") as f:
        msg = json.load(f)

    printer_type = msg.get("printer_type", "kitchen")
    job_id = msg.get("job_id")
    payload = msg.get("payload") or {}

    # config 로드 — --dump 모드는 config 없이도 동작 (기본값 사용)
    cfg_path = args.config or str(Path(__file__).resolve().parents[1] / "config.ini")
    if Path(cfg_path).exists():
        cfg = config_mod.load(cfg_path).printer
        codepage = cfg.codepage
        width = cfg.width
        escpos_id = cfg.escpos_codepage_id
        right_margin = cfg.right_margin
        body_font_size = cfg.body_font_size
        printer_name = cfg.name
    else:
        if not args.dump:
            print(
                f"config not found: {cfg_path}\n"
                "config.example.ini 를 config.ini 로 복사 후 값을 채우거나, "
                "--dump out.bin 으로 파일 출력만 시험할 수 있습니다.",
                file=sys.stderr,
            )
            return 2
        codepage, width, escpos_id, right_margin, body_font_size, printer_name = (
            "cp949", 48, None, 2, "double_height", "",
        )

    try:
        data = formatter_mod.build(
            payload,
            printer_type=printer_type,
            job_id=job_id,
            codepage=codepage,
            width=width,
            escpos_codepage_id=escpos_id,
            right_margin=right_margin,
            body_font_size=body_font_size,
        )
    except PrintError as e:
        print(f"format failed [{e.code}]: {e.message}", file=sys.stderr)
        return 3

    if args.dump:
        out = Path(args.dump)
        out.write_bytes(data)
        print(f"wrote {len(data)} bytes -> {out}")
        return 0

    try:
        printer_mod.print_raw(printer_name, data)
    except PrintError as e:
        print(f"print failed [{e.code}]: {e.message}", file=sys.stderr)
        return 4

    print(f"sent {len(data)} bytes to printer {printer_name!r}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
