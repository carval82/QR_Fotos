<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Moderación</title>
    <style>
        :root { --bg:#0b1220; --card:#0f1a30; --text:#e8eefc; --muted:#a9b7d6; --line:#203050; --accent:#4f8cff; --danger:#ff5a5f; --ok:#28c76f; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background: var(--bg); color: var(--text); }
        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }
        .container { max-width: 1100px; margin: 0 auto; padding: 24px 16px; }
        .card { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 16px; }
        .title { margin: 0 0 10px; font-size: 24px; }
        .subtitle { margin: 0 0 10px; color: var(--muted); font-weight: 600; }
        .nav { color: var(--muted); margin: 8px 0 16px; }
        .flash { background: rgba(79,140,255,.12); border: 1px solid rgba(79,140,255,.25); padding: 10px 12px; border-radius: 10px; margin: 12px 0; }
        .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px; }
        .photo-card { background: rgba(255,255,255,.02); border: 1px solid var(--line); border-radius: 12px; padding: 12px; }
        .photo-card img { width: 100%; height: auto; border-radius: 10px; display:block; }
        .btn { display:inline-block; padding: 10px 12px; border-radius: 10px; border: 1px solid var(--line); background: rgba(255,255,255,.04); color: var(--text); cursor: pointer; font-weight: 600; }
        .btn:hover { background: rgba(255,255,255,.07); }
        .btn-ok { border-color: rgba(40,199,111,.35); background: rgba(40,199,111,.15); }
        .btn-ok:hover { background: rgba(40,199,111,.24); }
        .btn-danger { border-color: rgba(255,90,95,.35); background: rgba(255,90,95,.15); }
        .btn-danger:hover { background: rgba(255,90,95,.24); }
        .thumbs { display:flex; flex-wrap:wrap; gap:10px; }
        .thumbs img { width:160px; height:120px; object-fit:cover; border-radius: 10px; border: 1px solid var(--line); }
        .brand { position: fixed; bottom: 16px; right: 16px; display: flex; align-items: center; gap: 8px; background: rgba(15,26,48,.85); border: 1px solid var(--line); border-radius: 10px; padding: 8px 12px; font-size: 12px; color: var(--muted); }
        .brand img { height: 28px; width: auto; }
        .brand span { white-space: nowrap; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="title">Moderación - {{ $event->name }}</h1>

        <div class="nav">
            <a href="{{ route('admin.events.index') }}">Volver a eventos</a>
            |
            <a href="{{ route('q.show', ['token' => $event->token]) }}" target="_blank">Link subida</a>
            |
            <a href="{{ route('screen.show', ['token' => $event->token]) }}" target="_blank">Pantalla</a>
        </div>

        @if(session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        <div class="card">
            <div class="subtitle">Pendientes</div>
            @if(!$pending->count())
                <div>No hay fotos pendientes.</div>
            @else
                <div class="grid">
                    @foreach($pending as $photo)
                        <div class="photo-card">
                            <img src="{{ $photo->url }}">
                            <div style="height:10px;"></div>
                            <form method="post" action="{{ route('admin.photos.approve', ['photo' => $photo->id]) }}" style="display:inline;">
                                @csrf
                                <button class="btn btn-ok" type="submit">Aprobar</button>
                            </form>
                            <form method="post" action="{{ route('admin.photos.reject', ['photo' => $photo->id]) }}" style="display:inline;">
                                @csrf
                                <button class="btn btn-danger" type="submit">Rechazar</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div style="height:16px;"></div>
        <div class="card">
            <div class="subtitle">Aprobadas (últimas 30)</div>
            @if(!$approved->count())
                <div>No hay aprobadas aún.</div>
            @else
                <div class="thumbs">
                    @foreach($approved as $photo)
                        <img src="{{ $photo->url }}">
                    @endforeach
                </div>
            @endif
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
</body>
</html>
