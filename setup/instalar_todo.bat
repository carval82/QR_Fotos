@echo off
:: ============================================
::   QR Fotos - Instalador Completo
::   Creado por LC Design
:: ============================================
:: Este script configura TODO automaticamente:
:: 1. Archivo hosts
:: 2. Hotspot de Windows
:: 3. Inicia Apache
:: Debe ejecutarse como Administrador

setlocal enabledelayedexpansion

title QR Fotos - Instalador Completo

echo.
echo  ============================================
echo     ____  ____    ______      __            
echo    / __ \/ __ \  / ____/___  / /_____  _____
echo   / / / / /_/ / / /_  / __ \/ __/ __ \/ ___/
echo  / /_/ / _, _/ / __/ / /_/ / /_/ /_/ (__  ) 
echo  \___\_\_/ ^|_^| /_/    \____/\__/\____/____/  
echo.                                            
echo     Sistema de Subida de Fotos por QR
echo     Creado por LC Design
echo  ============================================
echo.

:: Verificar si se ejecuta como administrador
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo  [ERROR] Este script debe ejecutarse como Administrador.
    echo.
    echo  Haz clic derecho en este archivo y selecciona:
    echo  "Ejecutar como administrador"
    echo.
    pause
    exit /b 1
)

:: Configuracion
set "SSID=QRFotos"
set "PASSWORD=qrfotos123"
set "HOTSPOT_IP=192.168.137.1"

echo  Configuracion del Hotspot:
echo  ------------------------------------------
echo    Nombre de red: %SSID%
echo    Contrasena:    %PASSWORD%
echo    IP servidor:   %HOTSPOT_IP%
echo  ------------------------------------------
echo.

set /p personalizar="  Deseas personalizar? (S/N): "
if /i "%personalizar%"=="S" (
    echo.
    set /p SSID="  Nombre de la red (SSID): "
    set /p PASSWORD="  Contrasena (min 8 chars): "
    echo.
)

echo.
echo  ============================================
echo     PASO 1: Configurando archivo hosts
echo  ============================================
echo.

set HOSTS_FILE=C:\Windows\System32\drivers\etc\hosts

findstr /C:"qrfotos.local" "%HOSTS_FILE%" >nul 2>&1
if %errorLevel% equ 0 (
    echo  [OK] qrfotos.local ya esta configurado
) else (
    echo.>> "%HOSTS_FILE%"
    echo # QR Fotos>> "%HOSTS_FILE%"
    echo 127.0.0.1 qrfotos.local>> "%HOSTS_FILE%"
    echo  [OK] qrfotos.local agregado al archivo hosts
)

echo.
echo  ============================================
echo     PASO 2: Configurando Mobile Hotspot
echo  ============================================
echo.

echo  Windows 10/11 requiere configurar el hotspot manualmente.
echo.
echo  Abriendo configuracion de Mobile Hotspot...
echo.
start ms-settings:network-mobilehotspot

echo  Por favor configura lo siguiente:
echo  ------------------------------------------
echo    1. Activa "Compartir mi conexion"
echo    2. Haz clic en "Editar"
echo    3. Nombre de red: %SSID%
echo    4. Contrasena: %PASSWORD%
echo    5. Banda: 2.4 GHz
echo    6. Guarda los cambios
echo  ------------------------------------------
echo.
echo  Presiona cualquier tecla cuando hayas terminado...
pause >nul

echo.
echo  ============================================
echo     PASO 3: Iniciando Apache
echo  ============================================
echo.

:: Verificar si Apache esta corriendo
tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I /N "httpd.exe">NUL
if %errorLevel% equ 0 (
    echo  [OK] Apache ya esta corriendo
) else (
    echo  Iniciando Apache...
    if exist "C:\xampp\apache\bin\httpd.exe" (
        start "" "C:\xampp\xampp-control.exe"
        echo  [OK] Panel de XAMPP abierto - Inicia Apache manualmente
    ) else (
        echo  [AVISO] No se encontro XAMPP en la ruta esperada
    )
)

echo.
echo  ============================================
echo     PASO 4: Verificando configuracion
echo  ============================================
echo.

:: Verificar virtual host
if exist "C:\xampp\apache\conf\extra\httpd-vhosts.conf" (
    findstr /C:"qrfotos.local" "C:\xampp\apache\conf\extra\httpd-vhosts.conf" >nul 2>&1
    if %errorLevel% equ 0 (
        echo  [OK] Virtual host configurado
    ) else (
        echo  [AVISO] Virtual host no configurado
        echo         Agrega la configuracion a httpd-vhosts.conf
    )
)

echo  [OK] Archivo hosts configurado
echo.

echo.
echo  ============================================
echo     INSTALACION COMPLETADA
echo  ============================================
echo.
echo  Datos para configurar en la pagina de QR:
echo  ------------------------------------------
echo    SSID:        %SSID%
echo    Contrasena:  %PASSWORD%
echo    IP servidor: %HOTSPOT_IP%
echo  ------------------------------------------
echo.
echo  URLs de acceso:
echo  ------------------------------------------
echo    Admin:    http://qrfotos.local/admin/login
echo    Pantalla: http://qrfotos.local/screen/{token}
echo  ------------------------------------------
echo.
echo  Credenciales de admin:
echo  ------------------------------------------
echo    Usuario:    admin
echo    Contrasena: admin
echo  ------------------------------------------
echo.

set /p abrir="  Abrir panel de admin ahora? (S/N): "
if /i "%abrir%"=="S" (
    start http://qrfotos.local/admin/login
)

echo.
echo  Gracias por usar QR Fotos!
echo  Creado por LC Design
echo.
pause
exit /b 0
