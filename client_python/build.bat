@echo off
REM Phase 3 D015 - PyInstaller build script
REM
REM Prereq: activated venv with client_python/requirements.txt + pyinstaller.
REM
REM Usage:
REM   cd client_python
REM   build.bat
REM
REM Output: client_python\dist\barorez-printer.exe

setlocal

cd /d "%~dp0"

echo === barorez-printer build start ===
echo.

REM Check PyInstaller
python -m pip show pyinstaller >NUL 2>&1
if errorlevel 1 (
    echo [info] PyInstaller not found, installing...
    python -m pip install pyinstaller
    if errorlevel 1 (
        echo [error] PyInstaller install failed
        exit /b 1
    )
)

echo [info] Ensure runtime dependencies...
python -m pip install -r requirements.txt
if errorlevel 1 (
    echo [error] requirements install failed
    exit /b 1
)

echo.
echo [info] Clean previous build artifacts...
if exist build rmdir /s /q build
if exist dist rmdir /s /q dist
if exist build_entry.py del /q build_entry.py

echo.
echo [info] Running PyInstaller...
python -m PyInstaller barorez-printer.spec --clean --noconfirm
if errorlevel 1 (
    echo [error] PyInstaller build failed
    exit /b 1
)

echo.
if exist dist\barorez-printer.exe (
    for %%F in (dist\barorez-printer.exe) do set EXE_SIZE=%%~zF
    echo === build success ===
    echo Output: %CD%\dist\barorez-printer.exe
    echo Size:   %EXE_SIZE% bytes
    echo.
    echo Next steps:
    echo   1. Copy dist\barorez-printer.exe to a target PC
    echo   2. Place config.ini next to the exe ^(or under %%LOCALAPPDATA%%\barorez-printer\^)
    echo   3. Run barorez-printer.exe and look for the tray icon
) else (
    echo [error] dist\barorez-printer.exe not produced.
    exit /b 1
)

endlocal
