"""시스템 트레이 UI (D014).

PRD §4.3 / §10.2.

상태별 아이콘 (16x16 단색 원 — Pillow 즉석 생성, Phase 5 에서 디자인 교체):
- 초록: connected (정상 연결 + 작업 처리 가능)
- 노랑: connecting / disconnected (재접속 백오프 중)
- 빨강: auth_failed / print_failed (즉시 해결 필요)

메뉴:
- 최근 출력 50건 (서브메뉴 — RecentBuffer 스냅샷)
- 상태 확인 (현재 상태 + 마지막 detail 표시)
- 재연결 (stop_event 토글로 즉시 재접속 강제)
- 로그 폴더 열기 (탐색기로 logs/)
- 버전 정보 (about)
- 종료 (stop_event set)

pystray 는 별도 스레드에서 GUI 루프를 돌린다 (asyncio 루프와 분리).
상태 변경은 asyncio 측에서 `set_status(status, detail)` 호출 → call_soon_threadsafe
없이 직접 pystray.Icon 의 속성을 갱신해도 안전 (pystray 가 내부에서 큐잉).
"""

from __future__ import annotations

import os
import subprocess
import sys
import threading
import time
from dataclasses import dataclass
from datetime import datetime
from io import BytesIO
from pathlib import Path
from typing import Callable

from PIL import Image, ImageDraw  # type: ignore[import-not-found]
import pystray  # type: ignore[import-not-found]

from . import logger as logger_mod

ICON_SIZE = 64

_COLORS = {
    "connected": (46, 160, 67),  # 초록
    "connecting": (212, 167, 44),  # 노랑
    "disconnected": (212, 167, 44),
    "auth_failed": (200, 50, 50),  # 빨강
    "print_failed": (200, 50, 50),
}

_LABELS = {
    "connected": "정상 연결됨",
    "connecting": "접속 중",
    "disconnected": "연결 끊김 (재접속 중)",
    "auth_failed": "인증 실패",
    "print_failed": "출력 실패",
}


def _make_icon(status: str) -> Image.Image:
    color = _COLORS.get(status, (128, 128, 128))
    img = Image.new("RGBA", (ICON_SIZE, ICON_SIZE), (0, 0, 0, 0))
    draw = ImageDraw.Draw(img)
    # 외곽 어두운 테두리 + 본체 단색 원
    draw.ellipse((2, 2, ICON_SIZE - 2, ICON_SIZE - 2), fill=color, outline=(30, 30, 30, 255), width=2)
    return img


@dataclass
class TrayController:
    icon: pystray.Icon
    on_reconnect: Callable[[], None]
    on_quit: Callable[[], None]
    logs_dir: Path
    app_version: str
    state_lock: threading.Lock
    current_status: str = "connecting"
    current_detail: str = ""
    started_at: float = 0.0

    def set_status(self, status: str, detail: str = "") -> None:
        with self.state_lock:
            self.current_status = status
            self.current_detail = detail
        try:
            self.icon.icon = _make_icon(status)
            self.icon.title = self._tooltip()
            self.icon.menu = self._build_menu()
        except Exception:  # pragma: no cover
            pass

    def _tooltip(self) -> str:
        label = _LABELS.get(self.current_status, self.current_status)
        if self.current_detail:
            return f"barorez-printer · {label} ({self.current_detail})"
        return f"barorez-printer · {label}"

    def _recent_entries_text(self) -> list[str]:
        entries = logger_mod.recent_buffer().snapshot()
        if not entries:
            return ["(아직 출력 이력 없음)"]
        out = []
        for e in entries:
            ts = datetime.fromtimestamp(e.ts).strftime("%m-%d %H:%M:%S")
            tag = {"printed": "OK", "duplicate": "DUP", "failed": "ERR"}.get(e.status, e.status)
            job = f"#{e.job_id}" if e.job_id is not None else "-"
            out.append(f"{ts}  [{tag}]  {job} {e.printer_type} · {e.detail}")
        return out

    def _build_menu(self) -> pystray.Menu:
        recent_items = []
        for line in self._recent_entries_text()[:20]:  # 메뉴는 상위 20건 (50건은 텍스트로 보여줌)
            recent_items.append(pystray.MenuItem(line, None, enabled=False))

        def _show_status_dialog(icon: pystray.Icon, item: pystray.MenuItem) -> None:
            # MessageBox 로 간단 표시
            with self.state_lock:
                label = _LABELS.get(self.current_status, self.current_status)
                detail = self.current_detail
            uptime_s = int(time.time() - self.started_at) if self.started_at else 0
            text = (
                f"상태: {label}\n"
                f"세부: {detail or '-'}\n"
                f"동작 시간: {uptime_s // 3600}시간 {(uptime_s % 3600) // 60}분"
            )
            _message_box("barorez-printer 상태", text)

        def _open_logs(icon: pystray.Icon, item: pystray.MenuItem) -> None:
            try:
                if sys.platform == "win32":
                    os.startfile(self.logs_dir)  # type: ignore[attr-defined]
                else:
                    subprocess.Popen(["xdg-open", str(self.logs_dir)])
            except Exception:
                pass

        def _show_about(icon: pystray.Icon, item: pystray.MenuItem) -> None:
            _message_box(
                "barorez-printer 버전 정보",
                f"버전: {self.app_version}\nphase 3 prototype",
            )

        def _do_reconnect(icon: pystray.Icon, item: pystray.MenuItem) -> None:
            self.on_reconnect()

        def _do_quit(icon: pystray.Icon, item: pystray.MenuItem) -> None:
            self.on_quit()
            icon.stop()

        return pystray.Menu(
            pystray.MenuItem("최근 출력 (상위 20건)", pystray.Menu(*recent_items) if recent_items else None, enabled=bool(recent_items)),
            pystray.MenuItem("상태 확인...", _show_status_dialog),
            pystray.Menu.SEPARATOR,
            pystray.MenuItem("재연결", _do_reconnect),
            pystray.MenuItem("로그 폴더 열기", _open_logs),
            pystray.Menu.SEPARATOR,
            pystray.MenuItem("버전 정보...", _show_about),
            pystray.MenuItem("종료", _do_quit),
        )


def _message_box(title: str, text: str) -> None:
    """간단한 정보 다이얼로그. Windows 는 user32 MessageBoxW, POSIX 는 print."""
    if sys.platform == "win32":
        try:
            import ctypes

            ctypes.windll.user32.MessageBoxW(0, text, title, 0x00000040)  # MB_ICONINFORMATION
            return
        except Exception:
            pass
    print(f"[{title}]\n{text}")


def build(
    *,
    logs_dir: Path,
    app_version: str,
    on_reconnect: Callable[[], None],
    on_quit: Callable[[], None],
) -> TrayController:
    icon_image = _make_icon("connecting")
    icon = pystray.Icon("barorez-printer", icon_image, title="barorez-printer · 시작 중")
    controller = TrayController(
        icon=icon,
        on_reconnect=on_reconnect,
        on_quit=on_quit,
        logs_dir=logs_dir,
        app_version=app_version,
        state_lock=threading.Lock(),
        started_at=time.time(),
    )
    icon.menu = controller._build_menu()
    return controller


def run_in_thread(controller: TrayController) -> threading.Thread:
    """pystray 의 메인 루프를 별도 스레드로 실행."""
    t = threading.Thread(target=controller.icon.run, name="tray", daemon=True)
    t.start()
    return t
