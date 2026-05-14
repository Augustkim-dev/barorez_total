"""로그 골격 (D013).

D014 에서 %LOCALAPPDATA% 일별 로테이션 + 30 일 보관으로 본격 정비.
지금은 stdout + 단일 파일 (state 와 같은 폴더의 client.log) 만.
"""

from __future__ import annotations

import logging
import sys
from pathlib import Path

_LOG_FORMAT = "%(asctime)s %(levelname)-5s [%(name)s] %(message)s"


def setup(log_dir: str | Path, *, level: int = logging.INFO) -> logging.Logger:
    log_path = Path(log_dir)
    log_path.mkdir(parents=True, exist_ok=True)

    root = logging.getLogger("barorez")
    root.setLevel(level)
    # 중복 핸들러 방지 — 재호출 시 기존 핸들러 제거
    for h in list(root.handlers):
        root.removeHandler(h)

    formatter = logging.Formatter(_LOG_FORMAT, datefmt="%Y-%m-%d %H:%M:%S")

    # Windows 기본 콘솔 인코딩(CP949) 에서 비-CP949 문자 출력 실패 방지.
    try:
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")  # type: ignore[attr-defined]
    except (AttributeError, OSError):
        pass
    stream = logging.StreamHandler(sys.stdout)
    stream.setFormatter(formatter)
    root.addHandler(stream)

    file_h = logging.FileHandler(log_path / "client.log", encoding="utf-8")
    file_h.setFormatter(formatter)
    root.addHandler(file_h)

    root.propagate = False
    return root


def get(name: str = "barorez") -> logging.Logger:
    return logging.getLogger(name)
