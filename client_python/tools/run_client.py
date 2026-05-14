"""barorez-printer 데몬 진입점 (D014 — 트레이 통합).

사용:
    python -m client_python.tools.run_client
    python -m client_python.tools.run_client --config "%LOCALAPPDATA%\\barorez-printer\\config.ini"
    python -m client_python.tools.run_client --no-tray   # 헤드리스 (백그라운드 검증용)

종료: 트레이 메뉴 "종료" 또는 Ctrl+C.
"""

from __future__ import annotations

import argparse
import asyncio
import signal
import sys
import threading
from pathlib import Path

APP_VERSION = "0.1.0-d016"


def _resolve_imports():
    try:
        from ..src import config as config_mod
        from ..src import logger as logger_mod
        from ..src import paths as paths_mod
        from ..src import state as state_mod
        from ..src import ws_client
        return config_mod, logger_mod, paths_mod, state_mod, ws_client
    except (ImportError, ValueError):
        sys.path.insert(0, str(Path(__file__).resolve().parents[2]))
        from client_python.src import config as config_mod  # type: ignore[no-redef]
        from client_python.src import logger as logger_mod  # type: ignore[no-redef]
        from client_python.src import paths as paths_mod  # type: ignore[no-redef]
        from client_python.src import state as state_mod  # type: ignore[no-redef]
        from client_python.src import ws_client  # type: ignore[no-redef]
        return config_mod, logger_mod, paths_mod, state_mod, ws_client


async def _amain(cfg_arg: str | None, with_tray: bool) -> int:
    config_mod, logger_mod, paths_mod, state_mod, ws_client = _resolve_imports()

    data = paths_mod.resolve(cfg_arg)
    if not data.config.exists():
        print(f"config not found: {data.config}", file=sys.stderr)
        print(
            "tools/register_client.py --write-config 로 생성하거나, "
            "config.example.ini 를 복사 후 값을 채우세요.",
            file=sys.stderr,
        )
        return 2
    data.ensure()

    cfg = config_mod.load(data.config)
    log = logger_mod.setup(data.logs_dir)

    if not cfg.server.ws_url:
        log.error("config [server].ws_url is empty")
        return 2
    if cfg.client.client_idx <= 0 or not cfg.client.token:
        log.error("config [client].client_idx / [client].token is missing — register 먼저")
        return 2
    if not cfg.printer.name:
        log.error("config [printer].name is missing")
        return 2

    state = state_mod.State.load(data.state_file)
    shop_tag = f"{cfg.client.shop_idx}/{cfg.client.shop_name}" if cfg.client.shop_name else str(cfg.client.shop_idx)
    log.info(
        "barorez-printer starting (version=%s, shop=%s, client_idx=%d, last_known_job_id=%s, data=%s)",
        APP_VERSION,
        shop_tag,
        cfg.client.client_idx,
        state.last_known_job_id,
        data.root,
    )

    stop_event = asyncio.Event()
    reconnect_event = asyncio.Event()
    loop = asyncio.get_running_loop()

    def _request_stop_threadsafe() -> None:
        loop.call_soon_threadsafe(stop_event.set)

    def _request_reconnect_threadsafe() -> None:
        loop.call_soon_threadsafe(reconnect_event.set)

    # POSIX signal 핸들러 (Windows 는 Ctrl+C 시 KeyboardInterrupt 만)
    for sig_name in ("SIGINT", "SIGTERM"):
        sig = getattr(signal, sig_name, None)
        if sig is None:
            continue
        try:
            loop.add_signal_handler(sig, stop_event.set)
        except NotImplementedError:
            pass

    # tray 구성
    tray_controller = None
    tray_thread: threading.Thread | None = None
    if with_tray:
        try:
            from ..src import tray as tray_mod
        except (ImportError, ValueError):
            from client_python.src import tray as tray_mod  # type: ignore[no-redef]

        tray_controller = tray_mod.build(
            logs_dir=data.logs_dir,
            app_version=APP_VERSION,
            on_reconnect=_request_reconnect_threadsafe,
            on_quit=_request_stop_threadsafe,
            shop_label=shop_tag,
        )
        tray_thread = tray_mod.run_in_thread(tray_controller)
        log.info("tray UI started")

    def _on_status(status: str, detail: str) -> None:
        # asyncio 측에서 호출 — tray 는 thread-safe 한 set_status
        if tray_controller is not None:
            tray_controller.set_status(status, detail)

    # ws_client 와 reconnect 신호 결합
    ws_task = asyncio.create_task(ws_client.run(cfg, state, stop_event=stop_event, on_status=_on_status))

    async def _reconnect_watcher() -> None:
        while not stop_event.is_set():
            try:
                await asyncio.wait_for(reconnect_event.wait(), timeout=1.0)
            except asyncio.TimeoutError:
                continue
            reconnect_event.clear()
            log.info("manual reconnect requested — restarting WS task")
            ws_task.cancel()

    watcher_task = asyncio.create_task(_reconnect_watcher())

    try:
        while not stop_event.is_set():
            try:
                await ws_task
                if stop_event.is_set():
                    break
                # ws_task 가 stop 없이 종료된 경우 (정상 close 후 재접속 루프 종료 등) — 재시작
                ws_task = asyncio.create_task(ws_client.run(cfg, state, stop_event=stop_event, on_status=_on_status))
            except asyncio.CancelledError:
                log.info("ws task cancelled — restarting")
                ws_task = asyncio.create_task(ws_client.run(cfg, state, stop_event=stop_event, on_status=_on_status))
    except KeyboardInterrupt:
        stop_event.set()
    finally:
        stop_event.set()
        watcher_task.cancel()
        ws_task.cancel()
        if tray_controller is not None:
            try:
                tray_controller.icon.stop()
            except Exception:
                pass
        if tray_thread is not None:
            tray_thread.join(timeout=2.0)
        log.info("barorez-printer stopped")
    return 0


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description="barorez-printer daemon (D014)")
    parser.add_argument("--config", default=None, help="config.ini 경로 (기본 자동 검색)")
    parser.add_argument("--no-tray", action="store_true", help="트레이 UI 비활성화 (헤드리스)")
    args = parser.parse_args(argv)

    try:
        return asyncio.run(_amain(args.config, with_tray=not args.no_tray))
    except KeyboardInterrupt:
        return 0


if __name__ == "__main__":
    sys.exit(main())
