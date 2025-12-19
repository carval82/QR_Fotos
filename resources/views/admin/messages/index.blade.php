<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mensajes - {{ $event->name }}</title>
    <style>
        :root { --bg:#0b1220; --card:#0f1a30; --text:#e8eefc; --muted:#a9b7d6; --line:#203050; --accent:#8b5cf6; --success:#28c76f; --danger:#ff5a5f; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: ui-sans-serif, system-ui, sans-serif; background: var(--bg); color: var(--text); padding: 24px; min-height: 100vh; }
        .container { max-width: 900px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
        .title { font-size: 24px; margin: 0; }
        .subtitle { color: var(--muted); font-size: 14px; margin-top: 4px; }
        .btn { padding: 10px 18px; border-radius: 10px; background: rgba(79,140,255,.18); border: 1px solid rgba(79,140,255,.35); color: var(--text); text-decoration: none; font-weight: 600; font-size: 14px; cursor: pointer; }
        .btn:hover { background: rgba(79,140,255,.28); }
        .btn-danger { background: rgba(255,90,95,.15); border-color: rgba(255,90,95,.35); }
        .btn-success { background: rgba(40,199,111,.15); border-color: rgba(40,199,111,.35); }
        .messages-grid { display: flex; flex-direction: column; gap: 16px; }
        .message-card { background: var(--card); border: 1px solid var(--line); border-radius: 16px; padding: 20px; position: relative; }
        .message-card.unread { border-left: 4px solid var(--accent); }
        .message-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
        .sender-name { font-size: 18px; font-weight: 600; color: var(--text); }
        .message-date { font-size: 12px; color: var(--muted); }
        .message-body { font-size: 15px; line-height: 1.6; color: var(--text); margin-bottom: 16px; white-space: pre-wrap; }
        .message-contact { display: flex; gap: 16px; flex-wrap: wrap; font-size: 13px; color: var(--muted); margin-bottom: 16px; }
        .message-contact a { color: var(--accent); text-decoration: none; }
        .message-contact a:hover { text-decoration: underline; }
        .message-actions { display: flex; gap: 8px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .badge-unread { background: rgba(139,92,246,.2); color: var(--accent); }
        .badge-read { background: rgba(40,199,111,.2); color: var(--success); }
        .empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
        .empty-state .icon { font-size: 48px; margin-bottom: 16px; }
        .stats { display: flex; gap: 16px; margin-bottom: 24px; }
        .stat-card { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 16px 20px; flex: 1; text-align: center; }
        .stat-number { font-size: 28px; font-weight: 700; color: var(--accent); }
        .stat-label { font-size: 12px; color: var(--muted); margin-top: 4px; }
        .brand { position: fixed; bottom: 16px; right: 16px; display: flex; align-items: center; gap: 8px; background: rgba(15,26,48,.85); border: 1px solid var(--line); border-radius: 10px; padding: 8px 12px; font-size: 12px; color: var(--muted); }
        .brand img { height: 24px; width: auto; }
        .developer { font-size: 10px; color: var(--muted); margin-top: 4px; }
        .developer a { color: var(--accent); text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1 class="title">💌 Mensajes</h1>
                <p class="subtitle">{{ $event->name }}</p>
            </div>
            <a href="{{ route('admin.events.index') }}" class="btn">← Volver a Eventos</a>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">{{ $messages->count() }}</div>
                <div class="stat-label">Total mensajes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $messages->where('is_read', false)->count() }}</div>
                <div class="stat-label">Sin leer</div>
            </div>
        </div>

        @if(session('status'))
            <div style="background: rgba(40,199,111,.15); border: 1px solid rgba(40,199,111,.35); padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; color: var(--success);">
                {{ session('status') }}
            </div>
        @endif

        <div class="messages-grid">
            @forelse($messages as $message)
                <div class="message-card {{ !$message->is_read ? 'unread' : '' }}">
                    <div class="message-header">
                        <div>
                            <span class="sender-name">{{ $message->sender_name }}</span>
                            @if(!$message->is_read)
                                <span class="badge badge-unread">Nuevo</span>
                            @else
                                <span class="badge badge-read">Leído</span>
                            @endif
                        </div>
                        <span class="message-date">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    <div class="message-body">{{ $message->message }}</div>
                    
                    @if($message->sender_phone || $message->sender_email)
                        <div class="message-contact">
                            @if($message->sender_phone)
                                <span>📱 <a href="tel:{{ $message->sender_phone }}">{{ $message->sender_phone }}</a></span>
                            @endif
                            @if($message->sender_email)
                                <span>✉️ <a href="mailto:{{ $message->sender_email }}">{{ $message->sender_email }}</a></span>
                            @endif
                        </div>
                    @endif
                    
                    <div class="message-actions">
                        @if(!$message->is_read)
                            <form method="POST" action="{{ route('admin.messages.read', $message) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success">✓ Marcar leído</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" style="display: inline;" onsubmit="return confirm('¿Eliminar este mensaje?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="icon">📭</div>
                    <h3>No hay mensajes aún</h3>
                    <p>Los mensajes de los invitados aparecerán aquí</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="brand">
        <img src="{{ asset('img/lcdesign-logo.png') }}" alt="LC Design">
        <div>
            <span>Creado por <strong>LC Design</strong></span>
            <div class="developer">
                Luis Carlos Correa · <a href="tel:3012481020">301 248 1020</a>
            </div>
        </div>
    </div>
</body>
</html>
