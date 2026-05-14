"""Print Spooler RAW 모드 어댑터.

매장의 기존 정품 드라이버를 그대로 활용하면서 ESC/POS 바이트열을
프린터에 직접 전달한다. PRD §4.3 — Windows Print Spooler raw 모드 우선.
"""

from __future__ import annotations

from typing import Any

from .errors import (
    PrinterOffline,
    PrinterOutOfPaper,
    UnknownPrintError,
)


# Windows GetPrinter() PRINTER_INFO_2.Status 비트 (winspool.h)
_PRINTER_STATUS_PAUSED = 0x00000001
_PRINTER_STATUS_ERROR = 0x00000002
_PRINTER_STATUS_PAPER_JAM = 0x00000008
_PRINTER_STATUS_PAPER_OUT = 0x00000010
_PRINTER_STATUS_PAPER_PROBLEM = 0x00000040
_PRINTER_STATUS_OFFLINE = 0x00000080
_PRINTER_STATUS_NOT_AVAILABLE = 0x00001000
_PRINTER_STATUS_NO_TONER = 0x00040000


def _classify_status(status: int) -> Exception | None:
    if status & (_PRINTER_STATUS_PAPER_OUT | _PRINTER_STATUS_PAPER_PROBLEM | _PRINTER_STATUS_PAPER_JAM):
        return PrinterOutOfPaper(f"printer status bits=0x{status:08x} (paper)")
    if status & (_PRINTER_STATUS_OFFLINE | _PRINTER_STATUS_NOT_AVAILABLE):
        return PrinterOffline(f"printer status bits=0x{status:08x} (offline)")
    return None


def list_installed() -> list[str]:
    """설치된 프린터 이름 목록. 설정 시 사용자가 정확한 이름을 입력하도록 돕는다."""
    import win32print  # type: ignore[import-not-found]

    flags = win32print.PRINTER_ENUM_LOCAL | win32print.PRINTER_ENUM_CONNECTIONS
    return [p[2] for p in win32print.EnumPrinters(flags)]


def print_raw(printer_name: str, data: bytes, *, doc_name: str = "barorez-receipt") -> None:
    """RAW bytes 를 Print Spooler 큐에 전달한다.

    실패 유형:
    - PrinterOffline: 프린터 열기 실패, 또는 상태 비트 OFFLINE
    - PrinterOutOfPaper: 상태 비트 용지 없음/잼
    - UnknownPrintError: 그 외
    """
    try:
        import win32print  # type: ignore[import-not-found]
    except ImportError as e:
        raise UnknownPrintError(f"pywin32 not installed: {e}") from e

    try:
        h_printer = win32print.OpenPrinter(printer_name)
    except Exception as e:  # pywintypes.error 포함
        raise PrinterOffline(f"OpenPrinter({printer_name!r}) failed: {e}") from e

    try:
        # 상태 비트 검사 (출력 전 사전 점검)
        info: dict[str, Any] = win32print.GetPrinter(h_printer, 2)
        status = int(info.get("Status", 0))
        classified = _classify_status(status)
        if classified is not None:
            raise classified

        try:
            job_id = win32print.StartDocPrinter(h_printer, 1, (doc_name, None, "RAW"))
        except Exception as e:
            raise UnknownPrintError(f"StartDocPrinter failed: {e}") from e

        try:
            win32print.StartPagePrinter(h_printer)
            try:
                win32print.WritePrinter(h_printer, data)
            finally:
                win32print.EndPagePrinter(h_printer)
        finally:
            win32print.EndDocPrinter(h_printer)
    finally:
        win32print.ClosePrinter(h_printer)
