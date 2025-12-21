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
        .upload-options { display: flex; gap: 10px; margin-bottom: 12px; }
        .upload-btn { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 16px 12px; border-radius: 12px; border: 1px dashed rgba(79,140,255,.35); background: rgba(255,255,255,.03); color: var(--text); cursor: pointer; transition: background .2s; }
        .upload-btn:hover { background: rgba(79,140,255,.12); }
        .upload-btn svg { width: 32px; height: 32px; margin-bottom: 8px; color: var(--accent); }
        .upload-btn span { font-size: 13px; font-weight: 600; }
        .upload-btn input { display: none; }
        .preview-area { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
        .preview-item { position: relative; width: 80px; height: 80px; border-radius: 8px; overflow: hidden; }
        .preview-item img { width: 100%; height: 100%; object-fit: cover; }
        .preview-item .remove { position: absolute; top: 4px; right: 4px; width: 20px; height: 20px; background: rgba(255,90,95,.9); border: none; border-radius: 50%; color: #fff; cursor: pointer; font-size: 14px; line-height: 1; }
        .file-count { color: var(--muted); font-size: 13px; margin-top: 8px; }
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
                <form method="post" action="{{ route('q.upload', ['token' => $event->token]) }}" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <label>Selecciona cómo subir tus fotos</label>
                    <div class="upload-options">
                        <label class="upload-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                            <span>Tomar Selfie</span>
                            <input type="file" accept="image/*" capture="user" id="cameraInput">
                        </label>
                        <label class="upload-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Galería</span>
                            <input type="file" accept="image/*" multiple id="galleryInput">
                        </label>
                    </div>

                    <div class="preview-area" id="previewArea"></div>
                    <div class="file-count" id="fileCount"></div>

                    <input type="file" name="photos[]" multiple accept="image/*" id="hiddenInput" style="display:none;">

                    @error('photos')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    @error('photos.*')
                        <div class="error">{{ $message }}</div>
                    @enderror

                    <button class="btn" type="submit" id="submitBtn" disabled>Subir</button>
                </form>

                <script>
                (function() {
                    const cameraInput = document.getElementById('cameraInput');
                    const galleryInput = document.getElementById('galleryInput');
                    const hiddenInput = document.getElementById('hiddenInput');
                    const previewArea = document.getElementById('previewArea');
                    const fileCount = document.getElementById('fileCount');
                    const submitBtn = document.getElementById('submitBtn');
                    const form = document.getElementById('uploadForm');

                    let selectedFiles = [];

                    function updateUI() {
                        previewArea.innerHTML = '';
                        selectedFiles.forEach((file, index) => {
                            const div = document.createElement('div');
                            div.className = 'preview-item';
                            const img = document.createElement('img');
                            img.src = URL.createObjectURL(file);
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'remove';
                            btn.innerHTML = '×';
                            btn.onclick = () => { selectedFiles.splice(index, 1); updateUI(); };
                            div.appendChild(img);
                            div.appendChild(btn);
                            previewArea.appendChild(div);
                        });
                        fileCount.textContent = selectedFiles.length > 0 ? selectedFiles.length + ' foto(s) seleccionada(s)' : '';
                        submitBtn.disabled = selectedFiles.length === 0;
                    }

                    function addFiles(files) {
                        for (let i = 0; i < files.length && selectedFiles.length < 10; i++) {
                            selectedFiles.push(files[i]);
                        }
                        updateUI();
                    }

                    cameraInput.addEventListener('change', (e) => { addFiles(e.target.files); e.target.value = ''; });
                    galleryInput.addEventListener('change', (e) => { addFiles(e.target.files); e.target.value = ''; });

                    form.addEventListener('submit', (e) => {
                        e.preventDefault();
                        const dt = new DataTransfer();
                        selectedFiles.forEach(f => dt.items.add(f));
                        hiddenInput.files = dt.files;
                        form.submit();
                    });
                })();
                </script>
                <div class="hint">Las fotos quedan en revisión antes de salir en pantalla.</div>
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
