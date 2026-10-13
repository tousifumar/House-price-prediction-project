@echo off
title House Price Predictor - Launcher
echo ========================================================
echo       HOUSE PRICE PREDICTOR - STARTING SERVICES
echo ========================================================
echo.

:: Detect PHP
set "PHP_BIN=php"
where php >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    if exist "C:\Users\Hp\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe" (
        set "PHP_BIN=C:\Users\Hp\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
    ) else (
        echo [ERROR] PHP was not found in PATH or WinGet package folder.
        pause
        exit /b 1
    )
)

:: Detect Python
set "PYTHON_BIN=python"
where python >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    if exist "C:\Program Files\Python313\python.exe" (
        set "PYTHON_BIN=C:\Program Files\Python313\python.exe"
    ) else (
        echo [ERROR] Python was not found in PATH or Program Files.
        pause
        exit /b 1
    )
)

echo [1/3] Starting Python ML Microservice on port 8001...
start "House Price Predictor - Python ML Service (Port 8001)" cmd /k "cd /d "%~dp0ml-service" && "%PYTHON_BIN%" -m uvicorn app.main:app --host 127.0.0.1 --port 8001"

echo [2/3] Starting Laravel 11 Backend on port 8000...
start "House Price Predictor - Laravel Web App (Port 8000)" cmd /k "cd /d "%~dp0laravel-app" && "%PHP_BIN%" artisan serve --host 127.0.0.1 --port 8000"

echo [3/3] Waiting 3 seconds for services to initialize...
timeout /t 3 /nobreak >nul

echo.
echo ========================================================
echo   Services are running!
echo   * Main Web Application: http://127.0.0.1:8000
echo   * Python ML Service:    http://127.0.0.1:8001
echo   * ML API Documentation: http://127.0.0.1:8001/docs
echo ========================================================
echo.
echo Opening http://127.0.0.1:8000 in your default browser...
start http://127.0.0.1:8000

echo.
echo You can keep this window open or close it. The service windows will remain active.
echo To stop the services, run stop-all.bat or close the individual service terminal windows.
pause