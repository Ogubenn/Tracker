@echo off
cd /d "%~dp0"
echo.
echo ========================================
echo   Atiksu Takip - Laravel Server
echo ========================================
echo.
echo Sunucu baslatiliyor...
echo http://127.0.0.1:8000
echo.
echo Durdurmak icin Ctrl+C basin
echo.
C:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe artisan serve
