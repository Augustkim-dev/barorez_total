"""PRD §8.6 출력 오류 분류.

ACK 메시지의 error_code 로 그대로 사용된다.
"""

from __future__ import annotations


class PrintError(Exception):
    code: str = "UNKNOWN"

    def __init__(self, message: str = "") -> None:
        super().__init__(message or self.code)
        self.message = message or self.code


class PrinterOffline(PrintError):
    code = "PRINTER_OFFLINE"


class PrinterOutOfPaper(PrintError):
    code = "PRINTER_OUT_OF_PAPER"


class UnsupportedEncoding(PrintError):
    code = "UNSUPPORTED_ENCODING"


class UnknownPrintError(PrintError):
    code = "UNKNOWN"
