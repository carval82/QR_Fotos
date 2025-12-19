<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Photo;
use Illuminate\Http\Request;

class PublicUploadController extends Controller
{
    public function show(string $token)
    {
        $event = Event::query()->where('token', $token)->firstOrFail();

        return view('q.upload', [
            'event' => $event,
        ]);
    }

    public function upload(Request $request, string $token)
    {
        $event = Event::query()->where('token', $token)->firstOrFail();

        if ($event->status !== 'active') {
            return redirect()->route('q.show', ['token' => $event->token])->with('status', 'Evento cerrado.');
        }

        $validated = $request->validate([
            'photos' => ['required', 'array', 'min:1', 'max:10'],
            'photos.*' => ['file', 'image', 'max:10240'],
        ]);

        $files = $validated['photos'];

        foreach ($files as $file) {
            $path = $file->store("events/{$event->id}", 'public');

            Photo::query()->create([
                'event_id' => $event->id,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'status' => $event->requires_moderation ? 'pending' : 'approved',
                'uploaded_ip' => $request->ip(),
            ]);
        }

        $message = $event->requires_moderation 
            ? 'Fotos subidas. Quedan en revisión.' 
            : 'Fotos subidas. ¡Ya están en pantalla!';

        return redirect()->route('q.show', ['token' => $event->token])->with('status', $message);
    }
}
