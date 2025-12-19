# QR Fotos - Guía de Instalación

## Configuración de Virtual Host en XAMPP (Windows)

### Paso 1: Editar archivo hosts de Windows

1. Abre **Bloc de notas como Administrador**
   - Click derecho en Bloc de notas → "Ejecutar como administrador"

2. Abre el archivo:
   ```
   C:\Windows\System32\drivers\etc\hosts
   ```

3. Agrega esta línea al final:
   ```
   127.0.0.1    qrfotos.local
   ```

4. Guarda el archivo

### Paso 2: Configurar Virtual Host en Apache

1. Abre el archivo de configuración de Apache:
   ```
   C:\xampp\apache\conf\extra\httpd-vhosts.conf
   ```

2. Agrega al final del archivo:
   ```apache
   <VirtualHost *:80>
       DocumentRoot "C:/xampp/htdocs/laravel/QR_Fotos/public"
       ServerName qrfotos.local
       <Directory "C:/xampp/htdocs/laravel/QR_Fotos/public">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

3. Guarda el archivo

### Paso 3: Reiniciar Apache

1. Abre el **Panel de Control de XAMPP**
2. Click en **Stop** en Apache
3. Click en **Start** en Apache

### Paso 4: Probar

Abre tu navegador y ve a:
- **Admin:** http://qrfotos.local/admin/events
- **Credenciales:** admin / admin

---

## Configuración para Hotspot (eventos sin internet)

Cuando uses el portátil como servidor con hotspot:

### En el archivo hosts del SERVIDOR (portátil):
```
127.0.0.1    qrfotos.local
```

### En el Virtual Host, cambia la IP para que escuche en todas las interfaces:

El virtual host ya está configurado para escuchar en `*:80`, así que funcionará.

### Configurar el QR

1. Ve a http://qrfotos.local/admin/events
2. Click en **QR** del evento
3. Configura:
   - **SSID:** El nombre de tu hotspot
   - **Contraseña:** La contraseña del hotspot
   - **IP del servidor:** `192.168.137.1` (IP típica del hotspot en Windows)

4. En la URL del QR, usa la IP en lugar del dominio:
   - `http://192.168.137.1/q/{token}`

---

## Comandos de instalación rápida

```bash
# Clonar repositorio
git clone https://github.com/carval82/QR_Fotos.git
cd QR_Fotos

# Instalar dependencias
composer install

# Configurar
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
```

## Credenciales por defecto

- **Usuario:** admin
- **Contraseña:** admin

Para cambiarlas, edita `.env`:
```
ADMIN_USER=tu_usuario
ADMIN_PASSWORD=tu_contraseña
```
