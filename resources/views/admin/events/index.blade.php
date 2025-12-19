<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Eventos</title>
    <style>
        :root { --bg:#0b1220; --card:#0f1a30; --text:#e8eefc; --muted:#a9b7d6; --line:#203050; --accent:#4f8cff; --danger:#ff5a5f; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background: var(--bg); color: var(--text); }
        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }
        .container { max-width: 1100px; margin: 0 auto; padding: 24px 16px; }
        .card { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 16px; }
        .row { display:flex; gap:16px; flex-wrap:wrap; }
        .grow { flex: 1 1 420px; }
        .title { margin: 0 0 12px; font-size: 24px; }
        .subtitle { margin: 0 0 10px; color: var(--muted); font-weight: 600; }
        .flash { background: rgba(79,140,255,.12); border: 1px solid rgba(79,140,255,.25); padding: 10px 12px; border-radius: 10px; margin: 12px 0; }
        .error { background: rgba(255,90,95,.12); border: 1px solid rgba(255,90,95,.25); padding: 10px 12px; border-radius: 10px; margin: 10px 0; }
        input[type="text"] { width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid var(--line); background: #0a1428; color: var(--text); }
        label { display:block; margin-bottom: 8px; color: var(--muted); }
        .btn { display:inline-block; padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(79,140,255,.35); background: rgba(79,140,255,.18); color: var(--text); cursor: pointer; font-weight: 600; }
        .btn:hover { background: rgba(79,140,255,.28); }
        table { width: 100%; border-collapse: collapse; overflow: hidden; border-radius: 12px; border: 1px solid var(--line); }
        th, td { padding: 10px 10px; border-bottom: 1px solid var(--line); vertical-align: top; }
        th { text-align: left; color: var(--muted); font-size: 13px; letter-spacing: .02em; background: rgba(255,255,255,.03); }
        tr:hover td { background: rgba(255,255,255,.02); }
        code { background: rgba(255,255,255,.06); padding: 3px 6px; border-radius: 8px; }
        .actions a { margin-right: 10px; }
        .checkbox-row { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
        .checkbox-row input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--accent); }
        .checkbox-row label { margin: 0; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .badge-yes { background: rgba(40,199,111,.18); color: #28c76f; }
        .badge-no { background: rgba(255,90,95,.18); color: #ff5a5f; }
        .brand { position: fixed; bottom: 16px; right: 16px; display: flex; align-items: center; gap: 8px; background: rgba(15,26,48,.85); border: 1px solid var(--line); border-radius: 10px; padding: 8px 12px; font-size: 12px; color: var(--muted); }
        .brand img { height: 28px; width: auto; }
        .brand span { white-space: nowrap; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h1 class="title" style="margin: 0;">Eventos</h1>
            <form method="POST" action="{{ route('admin.logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn" style="background: rgba(255,90,95,.15); border-color: rgba(255,90,95,.35);">Cerrar Sesión</button>
            </form>
        </div>

        @if(session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        <div class="row">
            <div class="card grow">
                <div class="subtitle">Crear evento</div>
                <form method="post" action="{{ route('admin.events.store') }}">
                    @csrf
                    <div style="margin-bottom:12px;">
                        <label>Nombre</label>
                        <input type="text" name="name" value="{{ old('name') }}" required>
                    </div>
                    @error('name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                    <div class="checkbox-row">
                        <input type="checkbox" name="requires_moderation" id="requires_moderation" value="1" checked>
                        <label for="requires_moderation">Requiere moderación</label>
                    </div>
                    <button class="btn" type="submit">Crear</button>
                </form>
            </div>
        </div>

        <div style="height:16px;"></div>
        <div class="card">
            <div class="subtitle">Lista</div>
            <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Status</th>
                <th>Moderación</th>
                <th>Token</th>
                <th>QR (link subida)</th>
                <th>Pantalla</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
                <tr>
                    <td>{{ $event->id }}</td>
                    <td>{{ $event->name }}</td>
                    <td>{{ $event->status }}</td>
                    <td>
                        @if($event->requires_moderation)
                            <span class="badge badge-yes">Sí</span>
                        @else
                            <span class="badge badge-no">No</span>
                        @endif
                    </td>
                    <td><code>{{ $event->token }}</code></td>
                    <td><a href="{{ route('q.show', ['token' => $event->token]) }}" target="_blank">Abrir</a></td>
                    <td><a href="{{ route('screen.show', ['token' => $event->token]) }}" target="_blank">Abrir</a></td>
                    <td class="actions">
                        <a href="{{ route('admin.events.qr', ['event' => $event->id]) }}">QR</a>
                        <a href="{{ route('admin.events.moderation', ['event' => $event->id]) }}">Moderar</a>
                        <a href="{{ route('admin.events.messages', ['event' => $event->id]) }}">Mensajes</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
            </table>
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
