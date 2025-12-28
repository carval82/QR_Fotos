<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Photo;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ScreenController extends Controller
{
    public function show(string $token)
    {
        $event = Event::query()->where('token', $token)->firstOrFail();

        return view('screen.show', [
            'event' => $event,
        ]);
    }

    public function photos(Request $request, string $token)
    {
        $event = Event::query()->where('token', $token)->firstOrFail();

        $since = $request->query('since');
        $sinceDt = null;
        if (is_string($since) && $since !== '') {
            try {
                $sinceDt = Carbon::parse($since);
            } catch (\Throwable $e) {
                $sinceDt = null;
            }
        }

        $query = Photo::query()
            ->where('event_id', $event->id)
            ->where('status', 'approved')
            ->orderBy('created_at');

        if ($sinceDt) {
            $query->where('created_at', '>', $sinceDt);
        } else {
            $query->limit(100);
        }

        $photos = $query->get();
        $lastCreatedAt = $photos->max('created_at');

        return response()->json([
            'photos' => $photos->map(fn (Photo $p) => [
                'id' => $p->id,
                'url' => $p->url,
                'created_at' => optional($p->created_at)->toISOString(),
            ])->values(),
            'last_created_at' => $lastCreatedAt ? $lastCreatedAt->toISOString() : $since,
        ]);
    }

    public function videos(Request $request, string $token)
    {
        $event = Event::query()->where('token', $token)->firstOrFail();

        $since = $request->query('since');
        $sinceDt = null;
        if (is_string($since) && $since !== '') {
            try {
                $sinceDt = Carbon::parse($since);
            } catch (\Throwable $e) {
                $sinceDt = null;
            }
        }

        $query = Video::query()
            ->where('event_id', $event->id)
            ->where('status', 'approved')
            ->orderBy('created_at');

        if ($sinceDt) {
            $query->where('created_at', '>', $sinceDt);
        } else {
            $query->limit(50);
        }

        $videos = $query->get();
        $lastCreatedAt = $videos->max('created_at');

        return response()->json([
            'videos' => $videos->map(fn (Video $v) => [
                'id' => $v->id,
                'url' => $v->url,
                'duration' => $v->duration,
                'created_at' => optional($v->created_at)->toISOString(),
            ])->values(),
            'last_created_at' => $lastCreatedAt ? $lastCreatedAt->toISOString() : $since,
        ]);
    }
}
