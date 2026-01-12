$host.UI.RawUI.WindowTitle = "Laravel Server - Atiksu Takip"
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   Atiksu Takip - Laravel Server" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Sunucu baslatiliyor..." -ForegroundColor Yellow
Write-Host "URL: http://127.0.0.1:8000" -ForegroundColor Green
Write-Host ""
Write-Host "Durdurmak icin Ctrl+C basin" -ForegroundColor Red
Write-Host ""

Set-Location $PSScriptRoot
& "C:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan serve
