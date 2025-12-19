@echo off
:: Script para configurar el archivo hosts de Windows
:: Debe ejecutarse como Administrador

echo ============================================
echo   QR Fotos - Configurador de Hosts
echo   Creado por LC Design
echo ============================================
echo.

:: Verificar si se ejecuta como administrador
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Este script debe ejecutarse como Administrador.
    echo.
    echo Haz clic derecho en este archivo y selecciona:
    echo "Ejecutar como administrador"
    echo.
    pause
    exit /b 1
)

set HOSTS_FILE=C:\Windows\System32\drivers\etc\hosts
set ENTRY=127.0.0.1 qrfotos.local

:: Verificar si ya existe la entrada
findstr /C:"%ENTRY%" "%HOSTS_FILE%" >nul 2>&1
if %errorLevel% equ 0 (
    echo [OK] La entrada "qrfotos.local" ya existe en el archivo hosts.
    echo.
    goto :menu
)

:: Agregar la entrada
echo.
echo Agregando entrada al archivo hosts...
echo.>> "%HOSTS_FILE%"
echo # QR Fotos - Sistema de subida de fotos por QR>> "%HOSTS_FILE%"
echo %ENTRY%>> "%HOSTS_FILE%"

if %errorLevel% equ 0 (
    echo [OK] Entrada agregada correctamente.
    echo.
    echo Ahora puedes acceder a: http://qrfotos.local/admin/events
) else (
    echo [ERROR] No se pudo agregar la entrada.
)

:menu
echo.
echo ============================================
echo   Opciones adicionales:
echo ============================================
echo.
echo 1. Abrir archivo hosts en Notepad
echo 2. Reiniciar Apache (XAMPP)
echo 3. Abrir navegador en qrfotos.local
echo 4. Salir
echo.
set /p opcion="Selecciona una opcion (1-4): "

if "%opcion%"=="1" goto :abrir_hosts
if "%opcion%"=="2" goto :reiniciar_apache
if "%opcion%"=="3" goto :abrir_navegador
if "%opcion%"=="4" goto :salir
goto :menu

:abrir_hosts
notepad "%HOSTS_FILE%"
goto :menu

:reiniciar_apache
echo.
echo Reiniciando Apache...
net stop Apache2.4 2>nul
net start Apache2.4 2>nul
if %errorLevel% neq 0 (
    echo.
    echo [INFO] Si Apache no reinicio, hazlo manualmente desde el panel de XAMPP.
)
goto :menu

:abrir_navegador
start http://qrfotos.local/admin/events
goto :menu

:salir
echo.
echo Gracias por usar QR Fotos!
echo.
pause
exit /b 0
