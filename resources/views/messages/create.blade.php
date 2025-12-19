<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dejar Mensaje - {{ $event->name }}</title>
    <style>
        :root {
            --bg: #0a0f1a;
            --card: #111827;
            --card-border: #1f2937;
            --text: #f3f4f6;
            --muted: #9ca3af;
            --accent: #8b5cf6;
            --accent-hover: #7c3aed;
            --success: #10b981;
            --gradient-start: #8b5cf6;
            --gradient-end: #ec4899;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        
        .bg-gradient {
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 30% 70%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 70% 30%, rgba(236, 72, 153, 0.15) 0%, transparent 50%);
            z-index: 0;
        }
        
        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
        }
        
        .card {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        
        .header {
            text-align: center;
            margin-bottom: 28px;
        }
        
        .header .icon {
            font-size: 48px;
            margin-bottom: 12px;
        }
        
        .header h1 {
            font-size: 24px;
            color: var(--text);
            margin-bottom: 8px;
        }
        
        .header p {
            color: var(--muted);
            font-size: 14px;
        }
        
        .event-name {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: var(--muted);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            color: var(--text);
            font-size: 15px;
            font-family: inherit;
            transition: all 0.2s ease;
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--accent);
            background: rgba(139, 92, 246, 0.1);
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15);
        }
        
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: var(--muted);
            opacity: 0.6;
        }
        
        .btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px -10px rgba(139, 92, 246, 0.5);
        }
        
        .success-message {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--success);
            padding: 16px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 15px;
        }
        
        .success-message .icon {
            font-size: 32px;
            display: block;
            margin-bottom: 8px;
        }
        
        .optional {
            font-size: 11px;
            color: var(--muted);
            font-weight: normal;
            text-transform: none;
            letter-spacing: 0;
        }
        
        .footer {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--card-border);
            text-align: center;
        }
        
        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: var(--muted);
            font-size: 12px;
            margin-bottom: 12px;
        }
        
        .brand img {
            height: 24px;
            width: auto;
            opacity: 0.8;
        }
        
        .developer {
            font-size: 11px;
            color: var(--muted);
            line-height: 1.6;
        }
        
        .developer a {
            color: var(--accent);
            text-decoration: none;
        }
        
        .developer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="bg-gradient"></div>
    
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="icon">💌</div>
                <h1>Deja tu mensaje</h1>
                <p>Para <span class="event-name">{{ $event->name }}</span></p>
            </div>
            
            @if(session('success'))
                <div class="success-message">
                    <span class="icon">✅</span>
                    {{ session('success') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('messages.store', $event->token) }}">
                @csrf
                
                <div class="form-group">
                    <label>Tu nombre *</label>
                    <input type="text" name="sender_name" placeholder="¿Cómo te llamas?" required maxlength="100">
                </div>
                
                <div class="form-group">
                    <label>Tu mensaje *</label>
                    <textarea name="message" placeholder="Escribe tu mensaje de felicitación..." required maxlength="1000"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Teléfono <span class="optional">(opcional)</span></label>
                    <input type="tel" name="sender_phone" placeholder="Tu número de contacto" maxlength="20">
                </div>
                
                <div class="form-group">
                    <label>Email <span class="optional">(opcional)</span></label>
                    <input type="email" name="sender_email" placeholder="tu@email.com" maxlength="100">
                </div>
                
                <button type="submit" class="btn">
                    Enviar Mensaje 💜
                </button>
            </form>
            
            <div class="footer">
                <div class="brand">
                    <img src="{{ asset('img/lcdesign-logo.png') }}" alt="LC Design">
                    <span>Creado por <strong>LC Design</strong></span>
                </div>
                <div class="developer">
                    <strong>Luis Carlos Correa Arrieta</strong><br>
                    Desarrollador de Software<br>
                    <a href="tel:3012481020">301 248 1020</a> · 
                    <a href="mailto:pcapacho24@gmail.com">pcapacho24@gmail.com</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
