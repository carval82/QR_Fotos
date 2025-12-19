<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Photo;
use Illuminate\Http\Request;

class AdminPhotoController extends Controller
{
    public function moderation(int $event)
    {
        $eventModel = Event::query()->findOrFail($event);

        $pending = Photo::query()
            ->where('event_id', $eventModel->id)
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $approved = Photo::query()
            ->where('event_id', $eventModel->id)
            ->where('status', 'approved')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        return view('admin.events.moderation', [
            'event' => $eventModel,
            'pending' => $pending,
            'approved' => $approved,
        ]);
    }

    public function approve(int $photo)
    {
        $photoModel = Photo::query()->findOrFail($photo);
        $photoModel->update(['status' => 'approved']);

        return back()->with('status', 'Foto aprobada.');
    }

    public function reject(int $photo)
    {
        $photoModel = Photo::query()->findOrFail($photo);
        $photoModel->update(['status' => 'rejected']);

        return back()->with('status', 'Foto rechazada.');
    }
}
