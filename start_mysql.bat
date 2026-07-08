@echo off
setlocal enabledelayedexpansion

set MYSQL_DIR=C:\laragon\bin\mysql\mysql-8.0.30-winx64
set DATA_DIR=C:\laragon\www\Taller\storage\mysql_data

REM Check if port 3307 is already in use
netstat -ano | findstr ":3307 " >nul 2>&1
if !ERRORLEVEL! EQU 0 exit /b 0

start "" /B "%MYSQL_DIR%\bin\mysqld.exe" --datadir="%DATA_DIR%" --basedir="%MYSQL_DIR%" --port=3307 --socket=mysql_taller.sock

REM Wait for MySQL to be ready
for /L %%i in (1,1,20) do (
    "%MYSQL_DIR%\bin\mysql.exe" -u root --port=3307 -h 127.0.0.1 -e "SELECT 1" >nul 2>&1
    if !ERRORLEVEL! EQU 0 exit /b 0
    ping -n 2 127.0.0.1 >nul
)
exit /b 1