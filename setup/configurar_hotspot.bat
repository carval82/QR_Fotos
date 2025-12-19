@echo off
:: ============================================
::   QR Fotos - Configurador de Hotspot
::   Creado por LC Design
:: ============================================
:: Este script configura automaticamente el hotspot de Windows
:: Debe ejecutarse como Administrador

setlocal enabledelayedexpansion

echo.
echo ============================================
echo   QR Fotos - Configurador de Hotspot
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

:: Configuracion por defecto
set "SSID=QRFotos"
set "PASSWORD=qrfotos123"

echo Configuracion actual:
echo   - Nombre de red (SSID): %SSID%
echo   - Contrasena: %PASSWORD%
echo.

:: Preguntar si quiere cambiar la configuracion
set /p cambiar="Deseas cambiar la configuracion? (S/N): "
if /i "%cambiar%"=="S" (
    echo.
    set /p SSID="Nombre de la red (SSID): "
    set /p PASSWORD="Contrasena (minimo 8 caracteres): "
    echo.
)

:: Verificar longitud de contrasena
call :strlen PASSWORD_LEN PASSWORD
if !PASSWORD_LEN! LSS 8 (
    echo [ERROR] La contrasena debe tener al menos 8 caracteres.
    pause
    exit /b 1
)

echo.
echo ============================================
echo   Configurando Hotspot...
echo ============================================
echo.

:: Detener hotspot si esta activo
echo [1/5] Deteniendo hotspot existente...
netsh wlan stop hostednetwork >nul 2>&1

:: Configurar el hotspot
echo [2/5] Configurando red: %SSID%
netsh wlan set hostednetwork mode=allow ssid="%SSID%" key="%PASSWORD%" >nul 2>&1
if %errorLevel% neq 0 (
    echo.
    echo [AVISO] El metodo clasico no funciono. Intentando con Mobile Hotspot...
    echo.
    goto :mobile_hotspot
)

:: Iniciar el hotspot
echo [3/5] Iniciando hotspot...
netsh wlan start hostednetwork
if %errorLevel% neq 0 (
    echo.
    echo [AVISO] No se pudo iniciar con hostednetwork. Intentando Mobile Hotspot...
    goto :mobile_hotspot
)

echo [4/5] Hotspot iniciado correctamente!
goto :configure_hosts

:mobile_hotspot
echo.
echo ============================================
echo   Configurando Mobile Hotspot (Windows 10/11)
echo ============================================
echo.
echo Windows 10/11 usa "Mobile Hotspot" que requiere configuracion manual.
echo.
echo Sigue estos pasos:
echo.
echo 1. Presiona Windows + I para abrir Configuracion
echo 2. Ve a: Red e Internet - Zona con cobertura inalambrica movil
echo 3. Activa "Compartir mi conexion a Internet"
echo 4. Haz clic en "Editar" y configura:
echo    - Nombre de red: %SSID%
echo    - Contrasena: %PASSWORD%
echo    - Banda: 2.4 GHz
echo 5. Guarda los cambios
echo.
echo Presiona cualquier tecla cuando hayas terminado...
pause >nul

:: Abrir configuracion de hotspot automaticamente
echo.
echo Abriendo configuracion de Mobile Hotspot...
start ms-settings:network-mobilehotspot

echo.
echo Espera a que configures el hotspot y luego presiona cualquier tecla...
pause >nul

:configure_hosts
echo.
echo [4/5] Configurando archivo hosts...

set HOSTS_FILE=C:\Windows\System32\drivers\etc\hosts
set ENTRY=127.0.0.1 qrfotos.local

:: Verificar si ya existe la entrada
findstr /C:"%ENTRY%" "%HOSTS_FILE%" >nul 2>&1
if %errorLevel% equ 0 (
    echo      - Entrada qrfotos.local ya existe
) else (
    echo.>> "%HOSTS_FILE%"
    echo # QR Fotos - Sistema de subida de fotos por QR>> "%HOSTS_FILE%"
    echo %ENTRY%>> "%HOSTS_FILE%"
    echo      - Entrada qrfotos.local agregada
)

echo.
echo [5/5] Obteniendo IP del hotspot...

:: Obtener la IP del adaptador de hotspot
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /C:"192.168.137"') do (
    set "HOTSPOT_IP=%%a"
    set "HOTSPOT_IP=!HOTSPOT_IP: =!"
)

if defined HOTSPOT_IP (
    echo      - IP del hotspot: !HOTSPOT_IP!
) else (
    set "HOTSPOT_IP=192.168.137.1"
    echo      - IP del hotspot (por defecto): !HOTSPOT_IP!
)

echo.
echo ============================================
echo   CONFIGURACION COMPLETADA
echo ============================================
echo.
echo Datos para el QR:
echo   - SSID: %SSID%
echo   - Contrasena: %PASSWORD%
echo   - IP del servidor: !HOTSPOT_IP!
echo.
echo URLs de acceso:
echo   - Admin: http://qrfotos.local/admin/login
echo   - Subida: http://!HOTSPOT_IP!/q/{token}
echo.
echo ============================================
echo.

:: Menu de opciones
:menu
echo Opciones:
echo   1. Abrir panel de admin en navegador
echo   2. Reiniciar Apache (XAMPP)
echo   3. Ver estado del hotspot
echo   4. Abrir configuracion de Mobile Hotspot
echo   5. Salir
echo.
set /p opcion="Selecciona una opcion (1-5): "

if "%opcion%"=="1" (
    start http://qrfotos.local/admin/login
    goto :menu
)
if "%opcion%"=="2" (
    echo Reiniciando Apache...
    net stop Apache2.4 2>nul
    timeout /t 2 >nul
    net start Apache2.4 2>nul
    echo.
    goto :menu
)
if "%opcion%"=="3" (
    echo.
    netsh wlan show hostednetwork
    echo.
    goto :menu
)
if "%opcion%"=="4" (
    start ms-settings:network-mobilehotspot
    goto :menu
)
if "%opcion%"=="5" (
    goto :end
)
goto :menu

:end
echo.
echo Gracias por usar QR Fotos!
echo Creado por LC Design
echo.
pause
exit /b 0

:: Funcion para calcular longitud de string
:strlen <resultVar> <stringVar>
setlocal enabledelayedexpansion
set "s=!%~2!#"
set "len=0"
for %%P in (4096 2048 1024 512 256 128 64 32 16 8 4 2 1) do (
    if "!s:~%%P,1!" NEQ "" ( 
        set /a "len+=%%P"
        set "s=!s:~%%P!"
    )
)
endlocal & set "%~1=%len%"
exit /b
