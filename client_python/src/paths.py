"""데이터 경로 결정 헬퍼.

운영 모드: `%LOCALAPPDATA%\\barorez-printer\\` 하위에 config / state / logs.
개발 모드: 저장소 안 `client_python/` 옆에서 config.ini 를 직접 들고 있는
경우, 같은 폴더의 state/ logs/ 사용. `run_client.py --config` 인자로 명시
지정하면 그 파일이 있는 폴더가 데이터 루트.

판단 우선순위:
1. --config 인자 → 그 파일이 데이터 루트
2. %LOCALAPPDATA%\\barorez-printer\\config.ini → LOCALAPPDATA 하위
3. <repo>/client_python/config.ini → client_python/ 하위 (개발 모드)
"""

from __future__ import annotations

import os
from dataclasses import dataclass
from pathlib import Path


PRODUCT = "barorez-printer"


@dataclass(frozen=True)
class DataPaths:
    root: Path
    config: Path
    state_dir: Path
    state_file: Path
    logs_dir: Path

    @classmethod
    def from_root(cls, root: Path, *, config_name: str = "config.ini") -> "DataPaths":
        return cls(
            root=root,
            config=root / config_name,
            state_dir=root / "state",
            state_file=root / "state" / "state.json",
            logs_dir=root / "logs",
        )

    def ensure(self) -> None:
        self.root.mkdir(parents=True, exist_ok=True)
        self.state_dir.mkdir(parents=True, exist_ok=True)
        self.logs_dir.mkdir(parents=True, exist_ok=True)


def localappdata_root() -> Path:
    base = os.environ.get("LOCALAPPDATA")
    if base:
        return Path(base) / PRODUCT
    # POSIX 또는 LOCALAPPDATA 미설정 — 홈 디렉터리 폴백
    return Path.home() / f".{PRODUCT}"


def repo_dev_root() -> Path:
    """이 모듈 위치 기준 client_python/ 디렉터리."""
    return Path(__file__).resolve().parents[1]


def resolve(explicit_config: str | os.PathLike | None) -> DataPaths:
    """config.ini 경로 결정 → 그 폴더를 데이터 루트로 사용."""
    if explicit_config:
        cfg = Path(explicit_config).resolve()
        return DataPaths.from_root(cfg.parent, config_name=cfg.name)

    appdata_cfg = localappdata_root() / "config.ini"
    if appdata_cfg.exists():
        return DataPaths.from_root(appdata_cfg.parent)

    dev_cfg = repo_dev_root() / "config.ini"
    if dev_cfg.exists():
        return DataPaths.from_root(dev_cfg.parent)

    # 둘 다 없음 — 우선 운영 위치를 반환 (호출자가 FileNotFoundError 처리)
    return DataPaths.from_root(localappdata_root())
