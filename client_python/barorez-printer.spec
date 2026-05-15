# -*- mode: python ; coding: utf-8 -*-
# PyInstaller spec — barorez-printer (Phase 3 D015)
#
# 빌드:
#   cd client_python
#   pyinstaller barorez-printer.spec --clean --noconfirm
#
# 결과물: client_python/dist/barorez-printer.exe (단일 파일, 콘솔 창 없음)

import sys
from pathlib import Path

block_cipher = None

# 진입점은 tools/run_client.py 의 main() — sys.exit 처리 포함된 __main__ 블록
# 으로 동작하도록 entry script 한 줄짜리 stub 작성.
HERE = Path(SPECPATH)
entry_stub = HERE / 'build_entry.py'
entry_stub.write_text(
    "import sys\n"
    "from client_python.tools.run_client import main\n"
    "sys.exit(main())\n",
    encoding='utf-8',
)

# certifi 의 cacert.pem 을 datas 로 명시 동봉. pyinstaller-hooks-contrib 의
# certifi hook 이 자동 처리하지만, hook 누락/버전 차이에 대비해 명시.
# frozen runtime 에서 certifi.where() 가 sys._MEIPASS\certifi\cacert.pem 을 반환.
import certifi
_CACERT = certifi.where()
_extra_datas = [(_CACERT, 'certifi')]

a = Analysis(
    [str(entry_stub)],
    pathex=[str(HERE.parent)],  # client_python 패키지가 import 가능하도록
    binaries=[],
    datas=_extra_datas,
    hiddenimports=[
        # pystray / Pillow / websockets / pywin32 의 일부 모듈은 PyInstaller
        # hook 으로 자동 감지되지만, 명시해두면 안전.
        'pystray._win32',
        'PIL.Image',
        'PIL.ImageDraw',
        'win32print',
        'win32api',
        'pywintypes',
        'websockets.legacy.client',
        'websockets.asyncio.client',
        # certifi — wss:// CA 번들. PyInstaller 의 certifi hook 이 자동으로
        # cacert.pem 을 datas 에 추가하지만 hiddenimport 도 안전을 위해 명시.
        'certifi',
    ],
    hookspath=[],
    hooksconfig={},
    runtime_hooks=[],
    excludes=[
        # 사이즈 절감 — 사용 안 하는 큰 의존성 제외.
        'tkinter',
        'unittest',
        'pydoc',
        'distutils',
    ],
    win_no_prefer_redirects=False,
    win_private_assemblies=False,
    cipher=block_cipher,
    noarchive=False,
)

pyz = PYZ(a.pure, a.zipped_data, cipher=block_cipher)

exe = EXE(
    pyz,
    a.scripts,
    a.binaries,
    a.zipfiles,
    a.datas,
    [],
    name='barorez-printer',
    debug=False,
    bootloader_ignore_signals=False,
    strip=False,
    upx=False,  # SmartScreen 우회 회피 — UPX 압축은 안티바이러스 오탐 유발 가능
    upx_exclude=[],
    runtime_tmpdir=None,
    console=False,         # 콘솔 창 없음 (트레이 전용)
    disable_windowed_traceback=False,
    argv_emulation=False,
    target_arch=None,
    codesign_identity=None,
    entitlements_file=None,
    # icon=str(HERE / 'icon.ico'),  # Phase 5 본 빌드 시 추가
)
