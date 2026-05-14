@echo off
REM Phase 3 D015 — PyInstaller 빌드 스크립트
REM
REM 전제: 활성화된 venv 에 client_python/requirements.txt + pyinstaller 설치.
REM
REM 사용:
REM   cd client_python
REM   build.bat
REM
REM 결과물: client_python\dist\barorez-printer.exe

setlocal

cd /d "%~dp0"

echo === barorez-printer 빌드 시작 ===
echo.

REM PyInstaller 설치 확인
python -m pip show pyinstaller >NUL 2>&1
if errorlevel 1 (
    echo [info] PyInstaller 가 설치되어 있지 않습니다. 설치합니다...
    python -m pip install pyinstaller
    if errorlevel 1 (
        echo [error] PyInstaller 설치 실패
        exit /b 1
    )
)

REM requirements 일괄 설치
echo [info] 의존성 설치 확인...
python -m pip install -r requirements.txt
if errorlevel 1 (
    echo [error] 의존성 설치 실패
    exit /b 1
)

echo.
echo [info] 이전 빌드 산출물 제거...
if exist build rmdir /s /q build
if exist dist rmdir /s /q dist
if exist build_entry.py del /q build_entry.py

echo.
echo [info] PyInstaller 빌드 시작...
python -m PyInstaller barorez-printer.spec --clean --noconfirm
if errorlevel 1 (
    echo [error] 빌드 실패
    exit /b 1
)

echo.
if exist dist\barorez-printer.exe (
    for %%F in (dist\barorez-printer.exe) do set EXE_SIZE=%%~zF
    echo === 빌드 성공 ===
    echo 산출물: %CD%\dist\barorez-printer.exe
    echo 크기: %EXE_SIZE% bytes
    echo.
    echo 다음 단계:
    echo   1) dist\barorez-printer.exe 를 별도 PC 에 복사
    echo   2) 같은 폴더에 config.ini 배치 ^(또는 %%LOCALAPPDATA%%\barorez-printer\config.ini^)
    echo   3) barorez-printer.exe 실행 — 트레이 아이콘 확인
) else (
    echo [error] dist\barorez-printer.exe 가 생성되지 않았습니다.
    exit /b 1
)

endlocal
