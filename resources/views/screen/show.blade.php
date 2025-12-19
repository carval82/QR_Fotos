<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pantalla - {{ $event->name }}</title>
    <style>
        html, body { height: 100%; margin: 0; background: #000; color: #fff; font-family: 'Segoe UI', system-ui, sans-serif; }
        
        #wrap { 
            height: 100%; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            position: relative;
        }
        
        #img { 
            max-width: 100vw; 
            max-height: 100vh; 
            object-fit: contain;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(1.02); }
            to { opacity: 1; transform: scale(1); }
        }
        
        /* Marca de agua grande en esquina inferior derecha */
        .watermark {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 24px;
            background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(0,0,0,0.5));
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            z-index: 100;
        }
        
        .watermark img {
            height: 50px;
            width: auto;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.5));
        }
        
        .watermark-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .watermark-text .created {
            font-size: 11px;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .watermark-text .company {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Info del evento en esquina superior izquierda */
        .event-info {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 12px 20px;
            background: rgba(0,0,0,0.6);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            backdrop-filter: blur(10px);
            z-index: 100;
        }
        
        .event-info .event-name {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
        }
        
        .event-info .photo-count {
            font-size: 12px;
            color: rgba(255,255,255,0.6);
            margin-top: 4px;
        }
        
        /* Mensaje cuando no hay fotos */
        .no-photos {
            text-align: center;
            color: rgba(255,255,255,0.5);
        }
        
        .no-photos h2 {
            font-size: 32px;
            margin-bottom: 12px;
            color: rgba(255,255,255,0.7);
        }
        
        .no-photos p {
            font-size: 18px;
        }
        
        /* QR pequeño para subir fotos */
        .qr-hint {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px;
            background: rgba(255,255,255,0.95);
            border-radius: 12px;
            text-align: center;
            z-index: 100;
        }
        
        .qr-hint canvas {
            display: block;
        }
        
        .qr-hint p {
            margin: 8px 0 0;
            font-size: 10px;
            color: #333;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div id="wrap">
    <div id="no-photos" class="no-photos" style="display: none;">
        <h2>📷 Esperando fotos...</h2>
        <p>Escanea el QR para subir fotos al evento</p>
    </div>
    <img id="img" src="" alt="" style="display: none;">
</div>

<div class="event-info">
    <div class="event-name">{{ $event->name }}</div>
    <div class="photo-count" id="photo-count">Cargando...</div>
</div>

<div class="watermark">
    <img src="{{ asset('img/lcdesign-logo.png') }}" alt="LC Design">
    <div class="watermark-text">
        <span class="created">Creado por</span>
        <span class="company">LC Design</span>
    </div>
</div>
<script>
    const token = @json($event->token);
    const apiUrl = @json(route('screen.photos', ['token' => $event->token]));

    let photos = [];
    let index = 0;
    let lastCreatedAt = null;

    function updateUI() {
        const img = document.getElementById('img');
        const noPhotos = document.getElementById('no-photos');
        const photoCount = document.getElementById('photo-count');
        
        photoCount.textContent = photos.length + ' foto' + (photos.length !== 1 ? 's' : '');
        
        if (!photos.length) {
            img.style.display = 'none';
            noPhotos.style.display = 'block';
            return;
        }
        
        noPhotos.style.display = 'none';
        img.style.display = 'block';
        
        if (index >= photos.length) index = 0;
        
        // Fade effect
        img.style.animation = 'none';
        img.offsetHeight; // Trigger reflow
        img.style.animation = 'fadeIn 0.5s ease';
        
        img.src = photos[index].url;
        img.alt = 'Foto ' + (index + 1);
        index++;
    }

    async function poll() {
        try {
            const url = new URL(apiUrl);
            if (lastCreatedAt) url.searchParams.set('since', lastCreatedAt);
            const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            if (Array.isArray(data.photos) && data.photos.length) {
                photos = photos.concat(data.photos);
                lastCreatedAt = data.last_created_at;
                if (photos.length === data.photos.length) {
                    updateUI();
                }
            }
            // Update count even if no new photos
            document.getElementById('photo-count').textContent = photos.length + ' foto' + (photos.length !== 1 ? 's' : '');
        } catch (e) {
            console.error('Error polling:', e);
        }
    }

    // Initial state
    document.getElementById('no-photos').style.display = 'block';
    document.getElementById('photo-count').textContent = '0 fotos';

    setInterval(updateUI, 5000);
    setInterval(poll, 3000);
    poll();
</script>
</body>
</html>
