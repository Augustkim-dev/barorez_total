"""Windows 자동 시작 등록/해제 (HKCU\\Software\\Microsoft\\Windows\\CurrentVersion\\Run).

사용:
    python -m client_python.tools.install_autostart --install
    python -m client_python.tools.install_autostart --install --config "C:\\path\\to\\config.ini"
    python -m client_python.tools.install_autostart --uninstall
    python -m client_python.tools.install_autostart --status

D015 의 PyInstaller .exe 가 만들어지면 그 .exe 경로로 자동 갱신해 부르는
방식이 자연스럽다. D014 단계에서는 현재 pythonw.exe + run_client.py
조합을 등록하여 venv 활성화 없이도 부팅 시 자동 기동되도록 한다.

레지스트리 값:
- 키: HKCU\\Software\\Microsoft\\Windows\\CurrentVersion\\Run
- 값 이름: barorez-printer
- 값 데이터: "<pythonw.exe>" -m client_python.tools.run_client [--config "<path>"]
"""

from __future__ import annotations

import argparse
import shlex
import sys
from pathlib import Path

REG_VALUE_NAME = "barorez-printer"
REG_RUN_PATH = r"Software\Microsoft\Windows\CurrentVersion\Run"


def _pythonw_path() -> str:
    """현재 venv 의 pythonw.exe (콘솔 창 없이 실행). 없으면 python.exe."""
    base = Path(sys.executable)
    if base.name.lower() == "pythonw.exe":
        return str(base)
    cand = base.with_name("pythonw.exe")
    return str(cand) if cand.exists() else str(base)


def _build_command(config_path: str | None) -> str:
    py = _pythonw_path()
    # python -m 은 cwd 의 패키지를 찾으므로, 저장소 루트를 cwd 로 지정해야 함.
    # 레지스트리 RUN 항목은 cwd 를 설정할 수 없으므로 -c 로 sys.path 보강.
    repo_root = str(Path(__file__).resolve().parents[2])
    cmd_inner = (
        f"import sys; sys.path.insert(0, r'{repo_root}'); "
        "from client_python.tools.run_client import main; sys.exit(main())"
    )
    parts = [f'"{py}"', "-c", _shell_escape(cmd_inner)]
    if config_path:
        parts.extend(["--", "--config", _shell_escape(config_path)])
    return " ".join(parts)


def _shell_escape(s: str) -> str:
    if " " in s or '"' in s:
        return '"' + s.replace('"', '\\"') + '"'
    return s


def install(config_path: str | None) -> int:
    if sys.platform != "win32":
        print("이 기능은 Windows 전용입니다.", file=sys.stderr)
        return 2
    import winreg  # type: ignore[import-not-found]

    cmd = _build_command(config_path)
    try:
        with winreg.OpenKey(winreg.HKEY_CURRENT_USER, REG_RUN_PATH, 0, winreg.KEY_SET_VALUE) as key:
            winreg.SetValueEx(key, REG_VALUE_NAME, 0, winreg.REG_SZ, cmd)
    except OSError as e:
        print(f"failed to write registry: {e}", file=sys.stderr)
        return 3

    print(f"installed: HKCU\\{REG_RUN_PATH}\\{REG_VALUE_NAME}")
    print(f"  -> {cmd}")
    return 0


def uninstall() -> int:
    if sys.platform != "win32":
        print("이 기능은 Windows 전용입니다.", file=sys.stderr)
        return 2
    import winreg  # type: ignore[import-not-found]

    try:
        with winreg.OpenKey(winreg.HKEY_CURRENT_USER, REG_RUN_PATH, 0, winreg.KEY_SET_VALUE) as key:
            try:
                winreg.DeleteValue(key, REG_VALUE_NAME)
            except FileNotFoundError:
                print("not installed (no-op)")
                return 0
    except OSError as e:
        print(f"failed to access registry: {e}", file=sys.stderr)
        return 3

    print(f"uninstalled: HKCU\\{REG_RUN_PATH}\\{REG_VALUE_NAME}")
    return 0


def status() -> int:
    if sys.platform != "win32":
        print("이 기능은 Windows 전용입니다.", file=sys.stderr)
        return 2
    import winreg  # type: ignore[import-not-found]

    try:
        with winreg.OpenKey(winreg.HKEY_CURRENT_USER, REG_RUN_PATH, 0, winreg.KEY_READ) as key:
            try:
                value, _ = winreg.QueryValueEx(key, REG_VALUE_NAME)
                print(f"installed: HKCU\\{REG_RUN_PATH}\\{REG_VALUE_NAME}")
                print(f"  -> {value}")
                return 0
            except FileNotFoundError:
                print("not installed")
                return 1
    except OSError as e:
        print(f"failed to access registry: {e}", file=sys.stderr)
        return 3


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description="Windows 자동 시작 등록/해제")
    group = parser.add_mutually_exclusive_group(required=True)
    group.add_argument("--install", action="store_true")
    group.add_argument("--uninstall", action="store_true")
    group.add_argument("--status", action="store_true")
    parser.add_argument(
        "--config",
        default=None,
        help="run_client.py --config 에 전달할 경로 (생략 시 자동 검색)",
    )
    args = parser.parse_args(argv)

    if args.install:
        return install(args.config)
    if args.uninstall:
        return uninstall()
    return status()


if __name__ == "__main__":
    sys.exit(main())
