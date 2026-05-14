"""설치된 프린터 이름 목록을 출력한다.

사용:
    python -m client_python.tools.list_printers
"""

from __future__ import annotations

import sys


def main() -> int:
    try:
        from src.printer import list_installed
    except ImportError:
        # 패키지 외부에서 직접 실행될 때
        from client_python.src.printer import list_installed  # type: ignore[no-redef]

    try:
        names = list_installed()
    except Exception as e:
        print(f"failed to enumerate printers: {e}", file=sys.stderr)
        return 2

    if not names:
        print("(no printers installed)")
        return 1
    for n in names:
        print(n)
    return 0


if __name__ == "__main__":
    sys.exit(main())
