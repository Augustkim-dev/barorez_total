r"""로그 시스템 (D014 본격 정비).

- 일별 파일 로테이션: YYYY-MM-DD.log → 30 일 보관 (자정 회전, backupCount=30)
- stdout 핸들러 (UTF-8 강제)
- ring buffer — 최근 50 건의 print_job 처리 결과 (트레이 메뉴 "최근 출력 50건" 용)

PRD §9.2 — `%LOCALAPPDATA%\barorez-printer\logs\YYYY-MM-DD.log`.
"""

from __future__ import annotations

import collections
import logging
import logging.handlers
import sys
import time
from dataclasses import dataclass
from pathlib import Path
from threading import Lock
from typing import Deque

_LOG_FORMAT = "%(asctime)s %(levelname)-5s [%(name)s] %(message)s"
_LOG_DATEFMT = "%Y-%m-%d %H:%M:%S"

RECENT_BUFFER_SIZE = 50


@dataclass(frozen=True)
class RecentEntry:
    ts: float  # epoch seconds
    job_id: int | None
    printer_type: str
    status: str  # 'printed' | 'failed' | 'duplicate'
    detail: str  # error_message / byte count


class RecentBuffer:
    """최근 출력 이력 ring buffer — 트레이 메뉴 표시용."""

    def __init__(self, max_len: int = RECENT_BUFFER_SIZE) -> None:
        self._buf: Deque[RecentEntry] = collections.deque(maxlen=max_len)
        self._lock = Lock()

    def add(self, entry: RecentEntry) -> None:
        with self._lock:
            self._buf.appendleft(entry)

    def snapshot(self) -> list[RecentEntry]:
        with self._lock:
            return list(self._buf)


_recent = RecentBuffer()


def recent_buffer() -> RecentBuffer:
    return _recent


def record(
    *,
    job_id: int | None,
    printer_type: str,
    status: str,
    detail: str,
) -> None:
    _recent.add(
        RecentEntry(
            ts=time.time(),
            job_id=job_id,
            printer_type=printer_type,
            status=status,
            detail=detail,
        )
    )


def setup(logs_dir: str | Path, *, level: int = logging.INFO) -> logging.Logger:
    log_path = Path(logs_dir)
    log_path.mkdir(parents=True, exist_ok=True)

    root = logging.getLogger("barorez")
    root.setLevel(level)
    for h in list(root.handlers):
        root.removeHandler(h)

    formatter = logging.Formatter(_LOG_FORMAT, datefmt=_LOG_DATEFMT)

    # stdout — UTF-8 강제 (Windows CP949 콘솔 회피)
    try:
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")  # type: ignore[attr-defined]
    except (AttributeError, OSError):
        pass
    stream = logging.StreamHandler(sys.stdout)
    stream.setFormatter(formatter)
    root.addHandler(stream)

    # 일별 로테이션 — when='midnight' 로 자정 회전, suffix 로 YYYY-MM-DD 부여
    base_file = log_path / "current.log"
    file_h = logging.handlers.TimedRotatingFileHandler(
        base_file,
        when="midnight",
        interval=1,
        backupCount=30,
        encoding="utf-8",
        utc=False,
    )
    file_h.suffix = "%Y-%m-%d"
    file_h.setFormatter(formatter)
    root.addHandler(file_h)

    root.propagate = False
    return root


def get(name: str = "barorez") -> logging.Logger:
    return logging.getLogger(name)
