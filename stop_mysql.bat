@echo off
setlocal enabledelayedexpansion

set MYSQL_DIR=C:\laragon\bin\mysql\mysql-8.0.30-winx64

"%MYSQL_DIR%\bin\mysqladmin.exe" -u root --port=3307 -h 127.0.0.1 shutdown >nul 2>&1
if !ERRORLEVEL! EQU 0 (
    echo [OK] MySQL en puerto 3307 detenido
) else (
    echo [..] MySQL no estaba corriendo en puerto 3307
)

pause