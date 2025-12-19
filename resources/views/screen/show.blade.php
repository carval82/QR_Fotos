<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pantalla</title>
    <style>
        html, body { height: 100%; margin: 0; background: #000; color: #fff; }
        #wrap { height: 100%; display: flex; align-items: center; justify-content: center; }
        #img { max-width: 100vw; max-height: 100vh; object-fit: contain; }
        #hint { position: fixed; bottom: 12px; left: 12px; font: 14px/1.2 sans-serif; opacity: .7; }
        .brand { position: fixed; bottom: 12px; right: 12px; display: flex; align-items: center; gap: 8px; background: rgba(0,0,0,.6); border: 1px solid rgba(255,255,255,.15); border-radius: 10px; padding: 8px 12px; font-size: 12px; color: rgba(255,255,255,.7); }
        .brand img { height: 28px; width: auto; }
        .brand span { white-space: nowrap; }
    </style>
</head>
<body>
<div id="wrap">
    <img id="img" src="" alt="">
</div>
<div id="hint">Evento: {{ $event->name }}</div>
<div class="brand">
    <img src="{{ asset('img/lcdesign-logo.png') }}" alt="LC Design">
    <span>Creado por <strong>LC Design</strong></span>
</div>
<script>
    const token = @json($event->token);
    const apiUrl = @json(route('screen.photos', ['token' => $event->token]));

    let photos = [];
    let index = 0;
    let lastCreatedAt = null;

    function showCurrent() {
        const img = document.getElementById('img');
        if (!photos.length) {
            img.removeAttribute('src');
            img.alt = 'Sin fotos aprobadas aún';
            return;
        }
        if (index >= photos.length) index = 0;
        img.src = photos[index].url;
        img.alt = 'foto';
        index++;
    }

    async function poll() {
        const url = new URL(apiUrl);
        if (lastCreatedAt) url.searchParams.set('since', lastCreatedAt);
        const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return;
        const data = await res.json();
        if (Array.isArray(data.photos) && data.photos.length) {
            photos = photos.concat(data.photos);
            lastCreatedAt = data.last_created_at;
            if (photos.length === data.photos.length) {
                showCurrent();
            }
        }
    }

    setInterval(showCurrent, 6000);
    setInterval(poll, 2500);
    poll();
</script>
</body>
</html>
