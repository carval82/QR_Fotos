<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminEventController extends Controller
{
    public function index()
    {
        $events = Event::query()->orderByDesc('id')->get();

        return view('admin.events.index', [
            'events' => $events,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'custom_token' => ['nullable', 'string', 'max:255', 'unique:events,token'],
        ]);

        $requiresModeration = $request->boolean('requires_moderation');

        if (!empty($validated['custom_token'])) {
            $token = $validated['custom_token'];
        } else {
            do {
                $token = Str::random(24);
            } while (Event::query()->where('token', $token)->exists());
        }

        Event::query()->create([
            'name' => $validated['name'],
            'token' => $token,
            'status' => 'active',
            'requires_moderation' => $requiresModeration,
        ]);

        return redirect()->route('admin.events.index')->with('status', 'Evento creado.');
    }

    public function qr(Event $event)
    {
        return view('admin.events.qr', [
            'event' => $event,
        ]);
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')->with('status', 'Evento eliminado correctamente.');
    }
}
