@echo off
REM ============================================================
REM start.bat — Inicia MySQL personalizado para el Taller
REM ============================================================
REM Este script verifica si MySQL en puerto 3307 ya está
REM corriendo; si no, lo inicia con los datos del proyecto.
REM ============================================================

set MYSQL_DIR=C:\laragon\bin\mysql\mysql-8.0.30-winx64
set DATA_DIR=C:\laragon\www\Taller\storage\mysql_data

REM Verificar si el puerto 3307 ya está en uso
netstat -ano | findstr ":3307 " >nul
if %ERRORLEVEL% EQU 0 (
    echo [OK] MySQL ya está corriendo en puerto 3307
    goto :end
)

REM Iniciar MySQL
echo [..] Iniciando MySQL en puerto 3307...
start "" /B "%MYSQL_DIR%\bin\mysqld.exe" --datadir="%DATA_DIR%" --basedir="%MYSQL_DIR%" --port=3307 --socket=mysql_taller.sock

REM Esperar a que MySQL esté listo
echo [..] Esperando conexión...
for /L %%i in (1,1,30) do (
    "%MYSQL_DIR%\bin\mysql.exe" -u root --port=3307 -h 127.0.0.1 -e "SELECT 1" >nul 2>&1
    if !ERRORLEVEL! EQU 0 (
        echo [OK] MySQL listo en puerto 3307
        goto :end
    )
    ping -n 2 127.0.0.1 >nul
)

:end
echo.
pause