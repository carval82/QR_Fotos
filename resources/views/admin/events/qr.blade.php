<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR - {{ $event->name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        :root { --bg:#0b1220; --card:#0f1a30; --text:#e8eefc; --muted:#a9b7d6; --line:#203050; --accent:#4f8cff; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background: var(--bg); color: var(--text); }
        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }
        .container { max-width: 900px; margin: 0 auto; padding: 24px 16px; }
        .title { margin: 0 0 6px; font-size: 28px; }
        .subtitle { margin: 0 0 20px; color: var(--muted); }
        .nav { margin-bottom: 20px; }
        .btn { display:inline-block; padding: 12px 18px; border-radius: 10px; border: 1px solid rgba(79,140,255,.35); background: rgba(79,140,255,.18); color: var(--text); cursor: pointer; font-weight: 600; text-decoration: none; }
        .btn:hover { background: rgba(79,140,255,.28); text-decoration: none; }
        .qr-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-top: 24px; }
        .qr-card { background: #fff; border-radius: 16px; padding: 24px; text-align: center; color: #111; }
        .qr-card h3 { margin: 0 0 8px; font-size: 18px; color: #333; }
        .qr-card p { margin: 0 0 16px; font-size: 13px; color: #666; }
        .qr-card canvas { display: block; margin: 0 auto; }
        .qr-card .url { margin-top: 12px; font-size: 11px; color: #888; word-break: break-all; }
        .config-section { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 20px; margin-top: 24px; }
        .config-section h3 { margin: 0 0 12px; color: var(--accent); }
        .config-section label { display: block; margin-bottom: 6px; color: var(--muted); font-size: 13px; }
        .config-section input { width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--line); background: #0a1428; color: var(--text); margin-bottom: 12px; }
        .brand { position: fixed; bottom: 16px; right: 16px; display: flex; align-items: center; gap: 8px; background: rgba(15,26,48,.85); border: 1px solid var(--line); border-radius: 10px; padding: 8px 12px; font-size: 12px; color: var(--muted); }
        .brand img { height: 28px; width: auto; }
        .brand span { white-space: nowrap; }
        .print-section { margin-top: 24px; text-align: center; }

        .print-layout { display: none; }
        
        @media print {
            body { background: #fff; color: #000; }
            .nav, .config-section, .print-section, .brand, .btn, .title, .subtitle, .qr-grid { display: none !important; }
            .container { max-width: 100%; padding: 20px; }
            
            .print-layout {
                display: flex;
                gap: 30px;
                align-items: flex-start;
                justify-content: space-between;
                min-height: 100vh;
            }
            
            .print-qr-section {
                flex: 0 0 auto;
                display: flex;
                flex-direction: column;
                gap: 15px;
            }
            
            .print-qr-card {
                background: #fff;
                border: 2px solid #ddd;
                border-radius: 8px;
                padding: 12px;
                text-align: center;
                max-width: 180px;
            }
            
            .print-qr-card h3 {
                margin: 0 0 4px;
                font-size: 13px;
                color: #333;
            }
            
            .print-qr-card p {
                margin: 0 0 8px;
                font-size: 10px;
                color: #666;
            }
            
            .print-image-section {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .print-image-section img {
                max-width: 100%;
                max-height: 95vh;
                width: auto;
                height: auto;
                object-fit: contain;
                border-radius: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav">
            <a href="{{ route('admin.events.index') }}">← Volver a eventos</a>
        </div>

        <h1 class="title">QR para: {{ $event->name }}</h1>
        <p class="subtitle">Escanea para conectarte al WiFi y subir fotos</p>

        <div class="config-section">
            <h3>Configuración WiFi del Hotspot</h3>
            <p style="color: var(--muted); font-size: 13px; margin-bottom: 16px;">
                Ingresa los datos del hotspot de tu portátil. El QR de WiFi se generará automáticamente.
            </p>
            <label>Nombre de la red (SSID)</label>
            <input type="text" id="wifi-ssid" value="QR_Fotos_Event" placeholder="Ej: MiHotspot">
            
            <label>Contraseña</label>
            <input type="text" id="wifi-pass" value="12345678" placeholder="Mínimo 8 caracteres">
            
            <label>IP del servidor (tu portátil)</label>
            <input type="text" id="server-ip" value="192.168.137.1" placeholder="Ej: 192.168.137.1">
            
            <button class="btn" onclick="generateQRs()">Regenerar QRs</button>
        </div>

        <div class="qr-grid">
            <div class="qr-card">
                <h3>1. Conectar al WiFi</h3>
                <p>Escanea para conectarte automáticamente</p>
                <div id="qr-wifi"></div>
                <div class="url" id="wifi-string"></div>
            </div>
            <div class="qr-card">
                <h3>2. Subir Fotos</h3>
                <p>Después de conectarte, escanea este QR</p>
                <div id="qr-url"></div>
                <div class="url" id="url-string"></div>
            </div>
            <div class="qr-card">
                <h3>3. Dejar Mensaje 💌</h3>
                <p>Escribe un mensaje de felicitación</p>
                <div id="qr-message"></div>
                <div class="url" id="message-string"></div>
            </div>
        </div>

        <div class="print-section">
            <button class="btn" onclick="window.print()">🖨️ Imprimir QRs</button>
        </div>

        <!-- Layout para impresión -->
        <div class="print-layout">
            <div class="print-qr-section">
                <div class="print-qr-card">
                    <h3>1. Conectar al WiFi</h3>
                    <p>Escanea para conectarte automáticamente</p>
                    <div id="qr-wifi-print"></div>
                </div>
                <div class="print-qr-card">
                    <h3>2. Subir Fotos</h3>
                    <p>Después de conectarte, escanea este QR</p>
                    <div id="qr-url-print"></div>
                </div>
                <div class="print-qr-card">
                    <h3>3. Dejar Mensaje 💌</h3>
                    <p>Escribe un mensaje de felicitación</p>
                    <div id="qr-message-print"></div>
                </div>
            </div>
            <div class="print-image-section">
                <img src="{{ asset('img/ana-lia-quince.jpg') }}" alt="Ana Lia - Mis Quince">
            </div>
        </div>

        <div class="config-section" style="margin-top: 32px;">
            <h3>📋 Cómo configurar el Hotspot en Windows</h3>
            <ol style="color: var(--muted); line-height: 1.8; padding-left: 20px;">
                <li>Abre <strong>Configuración</strong> → <strong>Red e Internet</strong> → <strong>Zona con cobertura inalámbrica móvil</strong></li>
                <li>Activa <strong>"Compartir mi conexión a Internet"</strong></li>
                <li>Haz clic en <strong>Editar</strong> para configurar:
                    <ul style="margin-top: 8px;">
                        <li><strong>Nombre de red:</strong> El mismo que pusiste arriba (SSID)</li>
                        <li><strong>Contraseña:</strong> La misma que pusiste arriba</li>
                        <li><strong>Banda:</strong> 2.4 GHz (mejor compatibilidad)</li>
                    </ul>
                </li>
                <li>La IP del hotspot suele ser <code style="background: rgba(255,255,255,.1); padding: 2px 6px; border-radius: 4px;">192.168.137.1</code></li>
                <li>Asegúrate de que XAMPP/Apache esté corriendo</li>
                <li>Los usuarios escanean el QR WiFi → luego el QR de URL → suben fotos</li>
            </ol>
        </div>
    </div>

    <div class="brand">
        <img src="{{ asset('img/lcdesign-logo.png') }}" alt="LC Design">
        <div>
            <span>Creado por <strong>LC Design</strong></span>
            <div style="font-size: 10px; margin-top: 4px;">
                Luis Carlos Correa · <a href="tel:3012481020" style="color: var(--accent);">301 248 1020</a>
            </div>
        </div>
    </div>

    <script>
        const eventToken = @json($event->token);
        let qrWifi = null;
        let qrUrl = null;
        let qrMessage = null;

        function generateQRs() {
            const ssid = document.getElementById('wifi-ssid').value;
            const pass = document.getElementById('wifi-pass').value;
            const serverIp = document.getElementById('server-ip').value;

            // WiFi QR string format: WIFI:T:WPA;S:<SSID>;P:<PASSWORD>;;
            const wifiString = `WIFI:T:WPA;S:${ssid};P:${pass};;`;
            document.getElementById('wifi-string').textContent = `Red: ${ssid}`;

            // URL for photo upload
            const uploadUrl = `http://${serverIp}/q/${eventToken}`;
            document.getElementById('url-string').textContent = uploadUrl;

            // URL for messages
            const messageUrl = `http://${serverIp}/m/${eventToken}`;
            document.getElementById('message-string').textContent = messageUrl;

            // Clear previous QRs (screen version)
            document.getElementById('qr-wifi').innerHTML = '';
            document.getElementById('qr-url').innerHTML = '';
            document.getElementById('qr-message').innerHTML = '';

            // Clear previous QRs (print version)
            document.getElementById('qr-wifi-print').innerHTML = '';
            document.getElementById('qr-url-print').innerHTML = '';
            document.getElementById('qr-message-print').innerHTML = '';

            // Generate WiFi QR (screen)
            qrWifi = new QRCode(document.getElementById('qr-wifi'), {
                text: wifiString,
                width: 200,
                height: 200,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });

            // Generate URL QR (screen)
            qrUrl = new QRCode(document.getElementById('qr-url'), {
                text: uploadUrl,
                width: 200,
                height: 200,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });

            // Generate Message QR (screen)
            qrMessage = new QRCode(document.getElementById('qr-message'), {
                text: messageUrl,
                width: 200,
                height: 200,
                colorDark: '#8b5cf6',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });

            // Generate WiFi QR (print)
            new QRCode(document.getElementById('qr-wifi-print'), {
                text: wifiString,
                width: 140,
                height: 140,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });

            // Generate URL QR (print)
            new QRCode(document.getElementById('qr-url-print'), {
                text: uploadUrl,
                width: 140,
                height: 140,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });

            // Generate Message QR (print)
            new QRCode(document.getElementById('qr-message-print'), {
                text: messageUrl,
                width: 140,
                height: 140,
                colorDark: '#8b5cf6',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        // Generate on page load
        document.addEventListener('DOMContentLoaded', generateQRs);
    </script>
</body>
</html>
