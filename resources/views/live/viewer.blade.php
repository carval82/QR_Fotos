<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transmisión en Vivo - {{ $event->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background: #000; color: white; overflow: hidden; }
        .container { width: 100vw; height: 100vh; display: flex; flex-direction: column; }
        .video-container { flex: 1; display: flex; align-items: center; justify-content: center; background: #000; position: relative; }
        video { width: 100%; height: 100%; object-fit: contain; }
        .status { position: absolute; top: 20px; left: 20px; background: rgba(0,0,0,0.7); padding: 12px 20px; border-radius: 50px; display: flex; align-items: center; gap: 8px; font-size: 14px; z-index: 10; }
        .status.live { background: rgba(239, 68, 68, 0.9); }
        .pulse { width: 12px; height: 12px; background: white; border-radius: 50%; animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        .waiting { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; z-index: 5; }
        .waiting h1 { font-size: 48px; margin-bottom: 16px; }
        .waiting p { font-size: 20px; color: #9ca3af; }
        .spinner { width: 60px; height: 60px; border: 4px solid rgba(255,255,255,0.1); border-top-color: white; border-radius: 50%; animation: spin 1s linear infinite; margin: 20px auto; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="container">
        <div class="video-container">
            <video id="remoteVideo" autoplay playsinline></video>
            
            <div class="waiting" id="waiting">
                <h1>📡</h1>
                <p>Esperando transmisión...</p>
                <div class="spinner"></div>
            </div>

            <div class="status" id="status" style="display: none;">
                <div class="pulse"></div>
                <span>EN VIVO</span>
            </div>
        </div>
    </div>

    <script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
    <script>
        const eventToken = @json($event->token);
        const remoteVideo = document.getElementById('remoteVideo');
        const waiting = document.getElementById('waiting');
        const status = document.getElementById('status');

        let peerConnection = null;
        let socket = null;

        const config = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' }
            ]
        };

        function init() {
            socket = io(window.location.origin);

            socket.emit('watcher', eventToken);

            socket.on('offer', async (id, description) => {
                peerConnection = new RTCPeerConnection(config);

                peerConnection.ontrack = (event) => {
                    remoteVideo.srcObject = event.streams[0];
                    waiting.style.display = 'none';
                    status.style.display = 'flex';
                    status.classList.add('live');
                };

                peerConnection.onicecandidate = (event) => {
                    if (event.candidate) {
                        socket.emit('candidate', id, event.candidate);
                    }
                };

                await peerConnection.setRemoteDescription(description);
                const answer = await peerConnection.createAnswer();
                await peerConnection.setLocalDescription(answer);
                socket.emit('answer', id, peerConnection.localDescription);
            });

            socket.on('candidate', async (id, candidate) => {
                if (peerConnection) {
                    await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
                }
            });

            socket.on('disconnectPeer', () => {
                if (peerConnection) {
                    peerConnection.close();
                }
                remoteVideo.srcObject = null;
                waiting.style.display = 'block';
                status.style.display = 'none';
            });
        }

        init();
    </script>
</body>
</html>
