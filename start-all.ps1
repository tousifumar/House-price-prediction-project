# House Price Predictor - PowerShell Launcher
Write-Host "========================================================" -ForegroundColor Cyan
Write-Host "      HOUSE PRICE PREDICTOR - STARTING SERVICES" -ForegroundColor Cyan
Write-Host "========================================================" -ForegroundColor Cyan
Write-Host ""

$rootDir = Split-Path -Parent $MyInvocation.MyCommand.Path

# Detect PHP
$phpCmd = Get-Command php -ErrorAction SilentlyContinue
if ($phpCmd) {
    $phpBin = $phpCmd.Source
} elseif (Test-Path "C:\Users\Hp\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe") {
    $phpBin = "C:\Users\Hp\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
} else {
    Write-Error "PHP was not found on your system."
    exit 1
}

# Detect Python
$pythonCmd = Get-Command python -ErrorAction SilentlyContinue
if ($pythonCmd) {
    $pythonBin = $pythonCmd.Source
} elseif (Test-Path "C:\Program Files\Python313\python.exe") {
    $pythonBin = "C:\Program Files\Python313\python.exe"
} else {
    Write-Error "Python was not found on your system."
    exit 1
}

Write-Host "[1/3] Starting Python ML Microservice on port 8001..." -ForegroundColor Yellow
Start-Process -FilePath "cmd.exe" -ArgumentList "/k cd /d `"$rootDir\ml-service`" && `"$pythonBin`" -m uvicorn app.main:app --host 127.0.0.1 --port 8001"

Write-Host "[2/3] Starting Laravel 11 Backend on port 8000..." -ForegroundColor Yellow
Start-Process -FilePath "cmd.exe" -ArgumentList "/k cd /d `"$rootDir\laravel-app`" && `"$phpBin`" artisan serve --host 127.0.0.1 --port 8000"

Write-Host "[3/3] Waiting 3 seconds for services to initialize..." -ForegroundColor Green
Start-Sleep -Seconds 3

Write-Host ""
Write-Host "========================================================" -ForegroundColor Green
Write-Host "  Services are live!" -ForegroundColor Green
Write-Host "  * Main Web App:       http://127.0.0.1:8000" -ForegroundColor White
Write-Host "  * Python ML Service:  http://127.0.0.1:8001" -ForegroundColor White
Write-Host "  * ML Swagger Docs:    http://127.0.0.1:8001/docs" -ForegroundColor White
Write-Host "========================================================" -ForegroundColor Green
Write-Host ""
Write-Host "Opening http://127.0.0.1:8000 in your default browser..." -ForegroundColor Cyan
Start-Process "http://127.0.0.1:8000"
