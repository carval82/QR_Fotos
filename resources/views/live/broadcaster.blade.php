<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transmitir en Vivo - {{ $event->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background: #000; color: white; overflow: hidden; }
        .container { width: 100vw; height: 100vh; display: flex; flex-direction: column; }
        .video-container { flex: 1; display: flex; align-items: center; justify-content: center; background: #000; position: relative; }
        video { width: 100%; height: 100%; object-fit: cover; }
        .controls { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.8)); padding: 20px; display: flex; gap: 12px; justify-content: center; align-items: center; }
        .btn { padding: 16px 32px; border: none; border-radius: 50px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px; }
        .btn-start { background: #ef4444; color: white; }
        .btn-start:hover { background: #dc2626; }
        .btn-stop { background: #6b7280; color: white; }
        .btn-stop:hover { background: #4b5563; }
        .btn-switch { background: rgba(255,255,255,0.2); color: white; padding: 12px; border-radius: 50%; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; }
        .status { position: absolute; top: 20px; left: 20px; background: rgba(0,0,0,0.7); padding: 12px 20px; border-radius: 50px; display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .status.live { background: rgba(239, 68, 68, 0.9); }
        .pulse { width: 12px; height: 12px; background: white; border-radius: 50%; animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        .info { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; }
        .info h1 { font-size: 32px; margin-bottom: 12px; }
        .info p { font-size: 16px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="video-container">
            <video id="localVideo" autoplay muted playsinline></video>
            
            <div class="info" id="info">
                <h1>🔴 Transmisión en Vivo</h1>
                <p>{{ $event->name }}</p>
            </div>

            <div class="status" id="status">
                <span id="statusText">Preparando...</span>
            </div>

            <div class="controls">
                <button class="btn btn-switch" id="switchCamera" title="Cambiar cámara">
                    🔄
                </button>
                <button class="btn btn-start" id="startBtn">
                    <span>▶</span> Iniciar Transmisión
                </button>
                <button class="btn btn-stop" id="stopBtn" style="display: none;">
                    <span>⏹</span> Detener
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
    <script>
        const eventToken = @json($event->token);
        const localVideo = document.getElementById('localVideo');
        const startBtn = document.getElementById('startBtn');
        const stopBtn = document.getElementById('stopBtn');
        const switchCamera = document.getElementById('switchCamera');
        const status = document.getElementById('status');
        const statusText = document.getElementById('statusText');
        const info = document.getElementById('info');

        let localStream = null;
        let peerConnection = null;
        let socket = null;
        let currentFacingMode = 'user';

        const config = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' }
            ]
        };

        async function initCamera() {
            try {
                const constraints = {
                    video: { 
                        facingMode: currentFacingMode,
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: true
                };

                localStream = await navigator.mediaDevices.getUserMedia(constraints);
                localVideo.srcObject = localStream;
                statusText.textContent = 'Listo para transmitir';
                info.style.display = 'none';
            } catch (error) {
                console.error('Error al acceder a la cámara:', error);
                statusText.textContent = 'Error: No se puede acceder a la cámara';
                alert('No se pudo acceder a la cámara. Verifica los permisos.');
            }
        }

        switchCamera.addEventListener('click', async () => {
            if (!localStream) return;
            
            currentFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
            
            localStream.getTracks().forEach(track => track.stop());
            await initCamera();
        });

        startBtn.addEventListener('click', async () => {
            startBtn.style.display = 'none';
            stopBtn.style.display = 'flex';
            status.classList.add('live');
            statusText.innerHTML = '<div class="pulse"></div> EN VIVO';

            // Conectar con el servidor WebSocket
            socket = io(window.location.origin);
            
            socket.emit('broadcaster', eventToken);

            socket.on('watcher', async (id) => {
                peerConnection = new RTCPeerConnection(config);
                
                localStream.getTracks().forEach(track => {
                    peerConnection.addTrack(track, localStream);
                });

                peerConnection.onicecandidate = (event) => {
                    if (event.candidate) {
                        socket.emit('candidate', id, event.candidate);
                    }
                };

                const offer = await peerConnection.createOffer();
                await peerConnection.setLocalDescription(offer);
                socket.emit('offer', id, peerConnection.localDescription);
            });

            socket.on('answer', async (id, description) => {
                await peerConnection.setRemoteDescription(description);
            });

            socket.on('candidate', (id, candidate) => {
                peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
            });
        });

        stopBtn.addEventListener('click', () => {
            if (socket) {
                socket.disconnect();
            }
            if (peerConnection) {
                peerConnection.close();
            }
            
            startBtn.style.display = 'flex';
            stopBtn.style.display = 'none';
            status.classList.remove('live');
            statusText.textContent = 'Transmisión detenida';
        });

        window.addEventListener('beforeunload', () => {
            if (localStream) {
                localStream.getTracks().forEach(track => track.stop());
            }
        });

        initCamera();
    </script>
</body>
</html>
