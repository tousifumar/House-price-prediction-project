@echo off
title House Price Predictor - Shutdown
echo ========================================================
echo       HOUSE PRICE PREDICTOR - STOPPING SERVICES
echo ========================================================
echo.

echo Stopping services on Port 8000 (Laravel) and Port 8001 (FastAPI)...

for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":8000" ^| findstr "LISTENING"') do (
    echo Terminating process listening on port 8000 (PID: %%a)...
    taskkill /F /PID %%a >nul 2>&1
)

for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":8001" ^| findstr "LISTENING"') do (
    echo Terminating process listening on port 8001 (PID: %%a)...
    taskkill /F /PID %%a >nul 2>&1
)

echo.
echo All services have been stopped.
pause
