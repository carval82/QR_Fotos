<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subir fotos</title>
    <style>
        :root { --bg:#0b1220; --card:#0f1a30; --text:#e8eefc; --muted:#a9b7d6; --line:#203050; --accent:#4f8cff; --danger:#ff5a5f; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background: linear-gradient(180deg, #0b1220, #070d18); color: var(--text); }
        .container { max-width: 720px; margin: 0 auto; padding: 24px 16px; }
        .card { background: rgba(15,26,48,.9); border: 1px solid var(--line); border-radius: 14px; padding: 16px; }
        .title { margin: 0 0 6px; font-size: 24px; }
        .subtitle { margin: 0 0 16px; color: var(--muted); }
        .flash { background: rgba(79,140,255,.12); border: 1px solid rgba(79,140,255,.25); padding: 10px 12px; border-radius: 10px; margin: 12px 0; }
        .error { background: rgba(255,90,95,.12); border: 1px solid rgba(255,90,95,.25); padding: 10px 12px; border-radius: 10px; margin: 10px 0; }
        label { display:block; margin-bottom: 8px; color: var(--muted); font-weight: 600; }
        input[type="file"] { width: 100%; padding: 12px; border-radius: 12px; border: 1px dashed rgba(79,140,255,.35); background: rgba(255,255,255,.03); color: var(--text); }
        .btn { display:inline-block; width: 100%; padding: 12px 14px; border-radius: 12px; border: 1px solid rgba(79,140,255,.35); background: rgba(79,140,255,.18); color: var(--text); cursor: pointer; font-weight: 700; margin-top: 12px; }
        .btn:hover { background: rgba(79,140,255,.28); }
        .hint { margin-top: 12px; color: var(--muted); }
        .closed { padding: 12px; border-radius: 12px; border: 1px solid rgba(255,90,95,.25); background: rgba(255,90,95,.10); }
        .brand { position: fixed; bottom: 16px; right: 16px; display: flex; align-items: center; gap: 8px; background: rgba(15,26,48,.85); border: 1px solid var(--line); border-radius: 10px; padding: 8px 12px; font-size: 12px; color: var(--muted); }
        .brand img { height: 28px; width: auto; }
        .brand span { white-space: nowrap; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1 class="title">Subir fotos</h1>
            <div class="subtitle">Evento: {{ $event->name }}</div>

            @if(session('status'))
                <div class="flash">{{ session('status') }}</div>
            @endif

            @if($event->status !== 'active')
                <div class="closed">Este evento está cerrado.</div>
            @else
                <form method="post" action="{{ route('q.upload', ['token' => $event->token]) }}" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <label>Selecciona fotos</label>
                        <input type="file" name="photos[]" multiple accept="image/*">
                    </div>

                    @error('photos')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    @error('photos.*')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    <button class="btn" type="submit">Subir</button>
                </form>
                <div class="hint">Las fotos quedan en revisión antes de salir en pantalla.</div>
            @endif
        </div>
    </div>
    <div class="brand">
        <img src="{{ asset('img/lcdesign-logo.png') }}" alt="LC Design">
        <span>Creado por <strong>LC Design</strong></span>
    </div>
</body>
</html>
