<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function show(string $token)
    {
        $event = Event::where('token', $token)->firstOrFail();
        return view('messages.create', compact('event'));
    }

    public function store(Request $request, string $token)
    {
        $event = Event::where('token', $token)->firstOrFail();

        $request->validate([
            'sender_name' => 'required|string|max:100',
            'message' => 'required|string|max:1000',
            'sender_phone' => 'nullable|string|max:20',
            'sender_email' => 'nullable|email|max:100',
        ]);

        Message::create([
            'event_id' => $event->id,
            'sender_name' => $request->sender_name,
            'message' => $request->message,
            'sender_phone' => $request->sender_phone,
            'sender_email' => $request->sender_email,
        ]);

        return back()->with('success', '¡Mensaje enviado! Tu mensaje será entregado.');
    }
}
