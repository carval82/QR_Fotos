<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Message;
use Illuminate\Http\Request;

class AdminMessageController extends Controller
{
    public function index(Event $event)
    {
        $messages = $event->messages()->latest()->get();
        return view('admin.messages.index', compact('event', 'messages'));
    }

    public function markAsRead(Message $message)
    {
        $message->update(['is_read' => true]);
        return back()->with('status', 'Mensaje marcado como leído');
    }

    public function destroy(Message $message)
    {
        $eventId = $message->event_id;
        $message->delete();
        return back()->with('status', 'Mensaje eliminado');
    }
}
