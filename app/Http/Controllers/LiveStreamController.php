<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class LiveStreamController extends Controller
{
    public function broadcaster(string $token)
    {
        $event = Event::query()->where('token', $token)->firstOrFail();

        return view('live.broadcaster', [
            'event' => $event,
        ]);
    }

    public function viewer(string $token)
    {
        $event = Event::query()->where('token', $token)->firstOrFail();

        return view('live.viewer', [
            'event' => $event,
        ]);
    }
}
