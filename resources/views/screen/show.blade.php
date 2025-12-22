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
        
        @keyframes newPhotoIn {
            0% { opacity: 0; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.05); }
            100% { opacity: 1; transform: scale(1); }
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
        <span style="font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 2px;">Luis Carlos Correa · 301 248 1020</span>
    </div>
</div>
<script>
    const token = @json($event->token);
    const apiUrl = @json(route('screen.photos', ['token' => $event->token]));

    let photos = [];
    let index = 0;
    let lastCreatedAt = null;
    
    // Sistema de cola de prioridad para fotos nuevas
    let newPhotosQueue = [];
    let slideInterval = null;
    let isShowingNewPhoto = false;
    const SLIDE_DURATION = 5000; // 5 segundos por foto
    const NEW_PHOTO_DURATION = 6000; // 6 segundos para fotos nuevas (un poco más)

    function showPhoto(photo, isNew = false) {
        const img = document.getElementById('img');
        const noPhotos = document.getElementById('no-photos');
        
        noPhotos.style.display = 'none';
        img.style.display = 'block';
        
        // Efecto de entrada especial para fotos nuevas
        img.style.animation = 'none';
        img.offsetHeight;
        img.style.animation = isNew ? 'newPhotoIn 0.8s ease' : 'fadeIn 0.5s ease';
        
        img.src = photo.url;
        img.alt = isNew ? '¡Nueva foto!' : 'Foto';
    }

    function updateCounter() {
        const photoCount = document.getElementById('photo-count');
        const total = photos.length;
        const pending = newPhotosQueue.length;
        
        if (pending > 0) {
            photoCount.textContent = total + ' foto' + (total !== 1 ? 's' : '') + ' • ' + pending + ' nueva' + (pending !== 1 ? 's' : '') + ' en cola';
        } else {
            photoCount.textContent = total + ' foto' + (total !== 1 ? 's' : '');
        }
    }

    function processQueue() {
        // Si hay fotos nuevas en cola, mostrarlas primero
        if (newPhotosQueue.length > 0) {
            isShowingNewPhoto = true;
            const newPhoto = newPhotosQueue.shift();
            showPhoto(newPhoto, true);
            updateCounter();
            
            // Después del tiempo de visualización, continuar con la cola o el carrusel
            setTimeout(() => {
                isShowingNewPhoto = false;
                processQueue();
            }, NEW_PHOTO_DURATION);
            return;
        }
        
        // Si no hay fotos nuevas, continuar con el carrusel normal
        if (photos.length > 0) {
            if (index >= photos.length) index = 0;
            showPhoto(photos[index], false);
            index++;
        }
    }

    function startCarousel() {
        // Limpiar intervalo anterior si existe
        if (slideInterval) clearInterval(slideInterval);
        
        // Iniciar carrusel normal
        slideInterval = setInterval(() => {
            // Solo avanzar si no estamos mostrando fotos nuevas
            if (!isShowingNewPhoto && newPhotosQueue.length === 0) {
                processQueue();
            }
        }, SLIDE_DURATION);
    }

    async function poll() {
        try {
            const url = new URL(apiUrl);
            if (lastCreatedAt) url.searchParams.set('since', lastCreatedAt);
            const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            
            if (Array.isArray(data.photos) && data.photos.length) {
                const isFirstLoad = photos.length === 0;
                
                // Agregar fotos al array principal
                photos = photos.concat(data.photos);
                lastCreatedAt = data.last_created_at;
                
                if (isFirstLoad) {
                    // Primera carga: mostrar primera foto inmediatamente
                    processQueue();
                } else {
                    // Fotos nuevas: agregar a la cola de prioridad
                    data.photos.forEach(photo => {
                        newPhotosQueue.push(photo);
                    });
                    
                    // Si no estamos mostrando una foto nueva, iniciar proceso inmediatamente
                    if (!isShowingNewPhoto) {
                        processQueue();
                    }
                }
                
                updateCounter();
            }
        } catch (e) {
            console.error('Error polling:', e);
        }
    }

    // Estado inicial
    document.getElementById('no-photos').style.display = 'block';
    document.getElementById('photo-count').textContent = '0 fotos';

    // Iniciar carrusel y polling
    startCarousel();
    setInterval(poll, 2000); // Polling cada 2 segundos para detectar fotos nuevas más rápido
    poll();
</script>
</body>
</html>
