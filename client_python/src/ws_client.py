"""Phase 3 D013 — Server C WebSocket 클라이언트.

흐름:
1. wss://.../printsvc/ws 접속
2. 첫 메시지로 auth 송신
3. auth_ok 수신 후 메시지 루프 진입
4. print_job 수신 시: 중복 체크 → ESC/POS 빌드 → RAW 출력 → ACK
5. ping 수신 시: 즉시 pong 회신
6. 연결 끊김 시 지수 백오프 재접속 (PRD §7.4 — 1s → 30s → 5min 상한)
7. 인증 관련 close code (4002/4003/4004) 는 즉시 재시도해도 의미 없으므로 60초 대기

중복 처리 (PRD §8.4) — 같은 job_id 24시간 내 재수신 시 출력 생략 + ACK 만.
재접속 동기화 — auth 메시지에 last_known_job_id 전송, Server C 의 sync 가
큐를 일괄 디스패치.
"""

from __future__ import annotations

import asyncio
import json
from datetime import datetime, timezone
from typing import Any, Callable

import websockets
from websockets.exceptions import ConnectionClosed

from . import formatter, logger as logger_mod, printer
from .config import AppConfig
from .errors import PrintError, UnknownPrintError
from .logger import get as get_logger
from .state import State

# 상태 콜백 — tray UI 가 받아 아이콘 색·툴팁 갱신.
StatusCallback = Callable[[str, str], None]
STATUS_CONNECTING = "connecting"
STATUS_CONNECTED = "connected"
STATUS_DISCONNECTED = "disconnected"
STATUS_AUTH_FAILED = "auth_failed"
STATUS_PRINT_FAILED = "print_failed"


def _emit(cb: StatusCallback | None, status: str, detail: str = "") -> None:
    if cb is None:
        return
    try:
        cb(status, detail)
    except Exception:  # pragma: no cover — tray 콜백 오류는 ws 루프를 막지 않음
        log.exception("status callback failed")

log = get_logger("barorez.ws")

BACKOFF_INITIAL_S = 1
BACKOFF_MAX_S = 300  # 5 minutes (PRD §7.4)
AUTH_ERROR_WAIT_S = 60
REPLACED_WAIT_S = 30
AUTH_FAIL_CLOSE_CODES = {4002, 4003, 4004}
REPLACED_CLOSE_CODE = 4005


def _iso_now() -> str:
    return datetime.now(timezone.utc).isoformat(timespec="seconds")


async def _send_json(ws: websockets.WebSocketClientProtocol, msg: dict[str, Any]) -> None:
    await ws.send(json.dumps(msg, ensure_ascii=False))


async def _send_auth(
    ws: websockets.WebSocketClientProtocol, cfg: AppConfig, state: State
) -> None:
    msg: dict[str, Any] = {
        "type": "auth",
        "client_id": cfg.client.client_idx,
        "token": cfg.client.token,
        "capabilities": list(cfg.client.capabilities),
        "app_version": cfg.client.app_version,
    }
    if state.last_known_job_id is not None:
        msg["last_known_job_id"] = state.last_known_job_id
    await _send_json(ws, msg)
    log.info(
        "auth sent client_id=%d caps=%s last_known_job_id=%s",
        cfg.client.client_idx,
        list(cfg.client.capabilities),
        state.last_known_job_id,
    )


async def _send_ack(
    ws: websockets.WebSocketClientProtocol,
    *,
    job_id: int,
    status: str,
    printed_at: str | None = None,
    error_code: str | None = None,
    error_message: str | None = None,
) -> None:
    msg: dict[str, Any] = {"type": "ack", "job_id": job_id, "status": status}
    if printed_at is not None:
        msg["printed_at"] = printed_at
    if error_code is not None:
        msg["error_code"] = error_code
    if error_message is not None:
        msg["error_message"] = error_message
    await _send_json(ws, msg)


async def _handle_print_job(
    msg: dict[str, Any],
    ws: websockets.WebSocketClientProtocol,
    cfg: AppConfig,
    state: State,
    on_status: StatusCallback | None,
) -> None:
    job_id = int(msg["job_id"])
    printer_type = str(msg.get("printer_type") or "kitchen")
    payload = msg.get("payload") or {}

    state.observe_job_id(job_id)

    if state.is_duplicate(job_id):
        prev_printed_at = state.get_printed_at(job_id)
        log.info("duplicate job_id=%d - ack only (printed_at=%s)", job_id, prev_printed_at)
        await _send_ack(
            ws,
            job_id=job_id,
            status="printed",
            printed_at=prev_printed_at or _iso_now(),
        )
        logger_mod.record(
            job_id=job_id, printer_type=printer_type, status="duplicate", detail="ack only"
        )
        state.save()
        return

    try:
        data = formatter.build(
            payload,
            printer_type=printer_type,
            job_id=job_id,
            codepage=cfg.printer.codepage,
            width=cfg.printer.width,
            escpos_codepage_id=cfg.printer.escpos_codepage_id,
            right_margin=cfg.printer.right_margin,
            body_font_size=cfg.printer.body_font_size,
        )
    except PrintError as e:
        log.error("job_id=%d format failed [%s] %s", job_id, e.code, e.message)
        await _send_ack(
            ws,
            job_id=job_id,
            status="failed",
            error_code=e.code,
            error_message=e.message,
        )
        state.record_processed(job_id, printed_at=None)
        state.save()
        logger_mod.record(
            job_id=job_id, printer_type=printer_type, status="failed", detail=f"{e.code}: {e.message}"
        )
        _emit(on_status, STATUS_PRINT_FAILED, e.code)
        return

    try:
        await asyncio.to_thread(
            printer.print_raw, cfg.printer.name, data, doc_name=f"barorez-job-{job_id}"
        )
    except PrintError as e:
        log.error("job_id=%d print failed [%s] %s", job_id, e.code, e.message)
        await _send_ack(
            ws,
            job_id=job_id,
            status="failed",
            error_code=e.code,
            error_message=e.message,
        )
        state.record_processed(job_id, printed_at=None)
        state.save()
        logger_mod.record(
            job_id=job_id, printer_type=printer_type, status="failed", detail=f"{e.code}: {e.message}"
        )
        _emit(on_status, STATUS_PRINT_FAILED, e.code)
        return
    except Exception as e:  # pragma: no cover — 예상치 못한 모든 오류 흡수
        log.exception("job_id=%d unexpected error", job_id)
        wrapped = UnknownPrintError(str(e))
        await _send_ack(
            ws,
            job_id=job_id,
            status="failed",
            error_code=wrapped.code,
            error_message=wrapped.message,
        )
        state.record_processed(job_id, printed_at=None)
        state.save()
        logger_mod.record(
            job_id=job_id, printer_type=printer_type, status="failed", detail=f"UNKNOWN: {e}"
        )
        _emit(on_status, STATUS_PRINT_FAILED, "UNKNOWN")
        return

    printed_at = _iso_now()
    state.record_processed(job_id, printed_at=printed_at)
    state.save()
    await _send_ack(ws, job_id=job_id, status="printed", printed_at=printed_at)
    log.info("job_id=%d printed (%d bytes)", job_id, len(data))
    logger_mod.record(
        job_id=job_id, printer_type=printer_type, status="printed", detail=f"{len(data)}B"
    )
    # 연결은 정상 — 출력 실패 상태에서 돌아왔다면 connected 로 복귀
    _emit(on_status, STATUS_CONNECTED, "")


async def _message_loop(
    ws: websockets.WebSocketClientProtocol,
    cfg: AppConfig,
    state: State,
    on_status: StatusCallback | None,
) -> None:
    async for raw in ws:
        text = raw.decode("utf-8") if isinstance(raw, (bytes, bytearray)) else raw
        try:
            msg = json.loads(text)
        except json.JSONDecodeError:
            log.warning("non-JSON message ignored: %r", text[:120])
            continue

        msg_type = msg.get("type")
        if msg_type == "ping":
            await _send_json(ws, {"type": "pong"})
            continue
        if msg_type == "print_job":
            asyncio.create_task(_handle_print_job(msg, ws, cfg, state, on_status))
            continue
        if msg_type == "auth_fail":
            reason = msg.get("reason", "")
            log.error("auth_fail received: %s", reason)
            continue
        if msg_type == "auth_ok":
            log.warning("late auth_ok ignored")
            continue
        log.debug("unknown message type=%r", msg_type)


async def _connect_once(
    cfg: AppConfig, state: State, on_status: StatusCallback | None
) -> int | None:
    """1회 접속 + auth + 메시지 루프. 종료된 close code 를 반환 (없으면 None)."""
    _emit(on_status, STATUS_CONNECTING, cfg.server.ws_url)
    async with websockets.connect(
        cfg.server.ws_url,
        open_timeout=10,
        ping_interval=None,  # Server C 가 ping 을 보내므로 클라이언트 측 ping 비활성화
        ping_timeout=None,
        max_size=2**20,
    ) as ws:
        log.info("connected to %s", cfg.server.ws_url)
        await _send_auth(ws, cfg, state)

        # 첫 메시지 = auth_ok / auth_fail
        first_raw = await ws.recv()
        first_text = first_raw.decode("utf-8") if isinstance(first_raw, (bytes, bytearray)) else first_raw
        first = json.loads(first_text)
        if first.get("type") != "auth_ok":
            log.error("expected auth_ok, got %r", first)
            # auth_fail 인 경우 곧 close 가 따라옴. 호출자에서 backoff 결정.
            try:
                await ws.wait_closed()
            except Exception:
                pass
            return ws.close_code

        log.info(
            "auth_ok client_id=%s shop_id=%s",
            first.get("client_id"),
            first.get("shop_id"),
        )
        _emit(on_status, STATUS_CONNECTED, f"shop_id={first.get('shop_id')}")

        try:
            await _message_loop(ws, cfg, state, on_status)
        except ConnectionClosed as e:
            return e.code
        return ws.close_code


async def run(
    cfg: AppConfig,
    state: State,
    *,
    stop_event: asyncio.Event | None = None,
    on_status: StatusCallback | None = None,
) -> None:
    """무한 재접속 루프. stop_event 가 set 되면 즉시 종료."""
    if stop_event is None:
        stop_event = asyncio.Event()

    backoff = BACKOFF_INITIAL_S
    while not stop_event.is_set():
        wait_s: float
        try:
            close_code = await _connect_once(cfg, state, on_status)
            log.info("connection closed code=%s", close_code)
            if close_code in AUTH_FAIL_CLOSE_CODES:
                log.error("auth-related close code %s — %ds 대기 후 재시도", close_code, AUTH_ERROR_WAIT_S)
                _emit(on_status, STATUS_AUTH_FAILED, f"close={close_code}")
                wait_s = AUTH_ERROR_WAIT_S
            elif close_code == REPLACED_CLOSE_CODE:
                log.warning("replaced by new connection — %ds 대기 후 재시도", REPLACED_WAIT_S)
                _emit(on_status, STATUS_DISCONNECTED, "replaced")
                wait_s = REPLACED_WAIT_S
            else:
                _emit(on_status, STATUS_DISCONNECTED, f"close={close_code}")
                wait_s = backoff
                backoff = min(backoff * 2, BACKOFF_MAX_S)
        except ConnectionClosed as e:
            log.warning("connection closed during handshake code=%s — backoff %ds", e.code, backoff)
            _emit(on_status, STATUS_DISCONNECTED, f"close={e.code}")
            wait_s = backoff
            backoff = min(backoff * 2, BACKOFF_MAX_S)
        except (OSError, asyncio.TimeoutError) as e:
            log.warning("connect failed: %s — backoff %ds", e, backoff)
            _emit(on_status, STATUS_DISCONNECTED, str(e)[:60])
            wait_s = backoff
            backoff = min(backoff * 2, BACKOFF_MAX_S)
        except Exception as e:  # pragma: no cover
            log.exception("unexpected error in connect loop — backoff %ds", backoff)
            _emit(on_status, STATUS_DISCONNECTED, str(e)[:60])
            wait_s = backoff
            backoff = min(backoff * 2, BACKOFF_MAX_S)
        else:
            if wait_s == backoff and close_code in (1000, 1001, None):
                backoff = BACKOFF_INITIAL_S

        try:
            await asyncio.wait_for(stop_event.wait(), timeout=wait_s)
            return
        except asyncio.TimeoutError:
            continue
