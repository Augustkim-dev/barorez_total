"""barorez-printer 데몬 진입점 (D013).

사용:
    python -m client_python.tools.run_client
    python -m client_python.tools.run_client --config client_python/config.ini

종료: Ctrl+C
"""

from __future__ import annotations

import argparse
import asyncio
import signal
import sys
from pathlib import Path


def _resolve_imports():
    try:
        from ..src import config as config_mod
        from ..src import logger as logger_mod
        from ..src import state as state_mod
        from ..src import ws_client
        return config_mod, logger_mod, state_mod, ws_client
    except (ImportError, ValueError):
        sys.path.insert(0, str(Path(__file__).resolve().parents[2]))
        from client_python.src import config as config_mod  # type: ignore[no-redef]
        from client_python.src import logger as logger_mod  # type: ignore[no-redef]
        from client_python.src import state as state_mod  # type: ignore[no-redef]
        from client_python.src import ws_client  # type: ignore[no-redef]
        return config_mod, logger_mod, state_mod, ws_client


async def _amain(cfg_path: Path, root_dir: Path) -> int:
    config_mod, logger_mod, state_mod, ws_client = _resolve_imports()

    cfg = config_mod.load(cfg_path)
    log = logger_mod.setup(root_dir / "state")

    if not cfg.server.ws_url:
        log.error("config [server].ws_url is empty")
        return 2
    if cfg.client.client_idx <= 0 or not cfg.client.token:
        log.error("config [client].client_idx / [client].token is missing — register 먼저")
        return 2
    if not cfg.printer.name:
        log.error("config [printer].name is missing")
        return 2

    state = state_mod.State.load(root_dir / "state" / "state.json")
    log.info(
        "barorez-printer starting (client_idx=%d, last_known_job_id=%s)",
        cfg.client.client_idx,
        state.last_known_job_id,
    )

    stop_event = asyncio.Event()

    def _request_stop() -> None:
        log.info("stop requested")
        stop_event.set()

    loop = asyncio.get_running_loop()
    # Windows 콘솔에서 Ctrl+C 시 KeyboardInterrupt 가 _amain 으로 전파됨.
    # 추가로 SIGTERM/SIGINT 핸들러도 등록 (POSIX 환경에서 동작).
    for sig_name in ("SIGINT", "SIGTERM"):
        sig = getattr(signal, sig_name, None)
        if sig is None:
            continue
        try:
            loop.add_signal_handler(sig, _request_stop)
        except NotImplementedError:
            pass  # Windows

    try:
        await ws_client.run(cfg, state, stop_event=stop_event)
    except KeyboardInterrupt:
        _request_stop()

    log.info("barorez-printer stopped")
    return 0


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description="barorez-printer daemon (D013)")
    parser.add_argument(
        "--config",
        default=None,
        help="config.ini 경로 (기본: client_python/config.ini)",
    )
    args = parser.parse_args(argv)

    root_dir = Path(__file__).resolve().parents[1]
    cfg_path = Path(args.config) if args.config else root_dir / "config.ini"
    if not cfg_path.exists():
        print(f"config not found: {cfg_path}", file=sys.stderr)
        return 2

    try:
        return asyncio.run(_amain(cfg_path, root_dir))
    except KeyboardInterrupt:
        return 0


if __name__ == "__main__":
    sys.exit(main())
