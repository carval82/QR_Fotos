<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Video;
use Illuminate\Http\Request;
use getID3;

class VideoController extends Controller
{
    public function show(string $token)
    {
        $event = Event::query()->where('token', $token)->firstOrFail();

        return view('videos.upload', [
            'event' => $event,
        ]);
    }

    public function upload(Request $request, string $token)
    {
        $event = Event::query()->where('token', $token)->firstOrFail();

        if ($event->status !== 'active') {
            return redirect()->route('videos.show', ['token' => $event->token])->with('status', 'Evento cerrado.');
        }

        $validated = $request->validate([
            'videos' => ['required', 'array', 'min:1', 'max:5'],
            'videos.*' => ['file', 'mimetypes:video/mp4,video/mpeg,video/quicktime,video/x-msvideo,video/webm', 'max:102400'],
        ]);

        $files = $validated['videos'];

        $eventDir = storage_path("app/public/videos/{$event->id}");
        if (!file_exists($eventDir)) {
            mkdir($eventDir, 0755, true);
        }

        foreach ($files as $file) {
            $duration = $this->getVideoDuration($file);

            if ($duration > 30) {
                return redirect()->route('videos.show', ['token' => $event->token])
                    ->with('error', 'El video no puede durar más de 30 segundos.');
            }

            $path = $file->store("videos/{$event->id}", 'public');

            Video::query()->create([
                'event_id' => $event->id,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'duration' => $duration,
                'status' => $event->requires_moderation ? 'pending' : 'approved',
                'uploaded_ip' => $request->ip(),
            ]);
        }

        $message = $event->requires_moderation 
            ? 'Videos subidos. Quedan en revisión.' 
            : 'Videos subidos. ¡Ya están en pantalla!';

        return redirect()->route('videos.show', ['token' => $event->token])->with('status', $message);
    }

    private function getVideoDuration($file): int
    {
        try {
            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($file->getRealPath());
            
            if (isset($fileInfo['playtime_seconds'])) {
                return (int) ceil($fileInfo['playtime_seconds']);
            }
        } catch (\Exception $e) {
            return 0;
        }

        return 0;
    }
}
