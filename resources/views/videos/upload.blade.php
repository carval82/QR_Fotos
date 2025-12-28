<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subir Videos - {{ $event->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; }
        .card { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { font-size: 28px; margin-bottom: 8px; color: #1a202c; }
        .subtitle { color: #718096; margin-bottom: 24px; font-size: 15px; }
        .status { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; }
        .upload-area { border: 3px dashed #cbd5e0; border-radius: 12px; padding: 40px 20px; text-align: center; margin-bottom: 20px; cursor: pointer; transition: all 0.3s; }
        .upload-area:hover { border-color: #667eea; background: #f7fafc; }
        .upload-area.dragover { border-color: #667eea; background: #edf2f7; }
        .upload-icon { font-size: 48px; margin-bottom: 12px; }
        .upload-text { color: #4a5568; font-size: 16px; margin-bottom: 8px; }
        .upload-hint { color: #a0aec0; font-size: 13px; }
        input[type="file"] { display: none; }
        .preview { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 12px; margin-bottom: 20px; }
        .preview-item { position: relative; border-radius: 10px; overflow: hidden; aspect-ratio: 16/9; background: #000; }
        .preview-item video { width: 100%; height: 100%; object-fit: cover; }
        .preview-item .remove { position: absolute; top: 8px; right: 8px; background: rgba(0,0,0,0.7); color: white; border: none; border-radius: 50%; width: 28px; height: 28px; cursor: pointer; font-size: 18px; }
        .preview-item .duration { position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 6px; font-size: 11px; }
        .btn { width: 100%; padding: 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: transform 0.2s; }
        .btn:hover { transform: translateY(-2px); }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .info { background: #e6fffa; border: 1px solid #81e6d9; color: #234e52; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>📹 Subir Videos</h1>
            <p class="subtitle">{{ $event->name }}</p>

            @if(session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            @if(session('error'))
                <div class="error">{{ session('error') }}</div>
            @endif

            <div class="info">
                ⏱️ Máximo 30 segundos por video<br>
                📦 Hasta 5 videos a la vez<br>
                💾 Tamaño máximo: 100MB por video
            </div>

            <form method="post" action="{{ route('videos.upload', ['token' => $event->token]) }}" enctype="multipart/form-data" id="uploadForm">
                @csrf
                
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">🎬</div>
                    <div class="upload-text">Toca para seleccionar videos</div>
                    <div class="upload-hint">o arrastra y suelta aquí</div>
                    <input type="file" name="videos[]" id="videoInput" accept="video/*" multiple>
                </div>

                <div class="preview" id="preview"></div>

                <button type="submit" class="btn" id="submitBtn" disabled>Subir Videos</button>
            </form>
        </div>
    </div>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const videoInput = document.getElementById('videoInput');
        const preview = document.getElementById('preview');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('uploadForm');
        let selectedFiles = [];

        uploadArea.addEventListener('click', () => videoInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        videoInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            const fileArray = Array.from(files);
            
            if (selectedFiles.length + fileArray.length > 5) {
                alert('Máximo 5 videos a la vez');
                return;
            }

            fileArray.forEach(file => {
                if (!file.type.startsWith('video/')) {
                    alert('Solo se permiten archivos de video');
                    return;
                }

                if (file.size > 100 * 1024 * 1024) {
                    alert('El video ' + file.name + ' es muy grande (máx 100MB)');
                    return;
                }

                selectedFiles.push(file);
                createPreview(file);
            });

            updateSubmitButton();
        }

        function createPreview(file) {
            const item = document.createElement('div');
            item.className = 'preview-item';

            const video = document.createElement('video');
            video.src = URL.createObjectURL(file);
            video.muted = true;

            video.addEventListener('loadedmetadata', () => {
                const duration = Math.ceil(video.duration);
                
                if (duration > 30) {
                    alert('El video ' + file.name + ' dura más de 30 segundos');
                    removeFile(file);
                    item.remove();
                    return;
                }

                const durationLabel = document.createElement('div');
                durationLabel.className = 'duration';
                durationLabel.textContent = duration + 's';
                item.appendChild(durationLabel);
            });

            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove';
            removeBtn.innerHTML = '×';
            removeBtn.type = 'button';
            removeBtn.onclick = () => {
                removeFile(file);
                item.remove();
            };

            item.appendChild(video);
            item.appendChild(removeBtn);
            preview.appendChild(item);
        }

        function removeFile(file) {
            selectedFiles = selectedFiles.filter(f => f !== file);
            updateSubmitButton();
        }

        function updateSubmitButton() {
            submitBtn.disabled = selectedFiles.length === 0;
        }

        form.addEventListener('submit', (e) => {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            videoInput.files = dataTransfer.files;
        });
    </script>
</body>
</html>
