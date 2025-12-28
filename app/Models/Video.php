<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'event_id',
        'path',
        'original_name',
        'mime',
        'size',
        'duration',
        'status',
        'uploaded_ip',
    ];

    protected $appends = [
        'url',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getUrlAttribute(): string
    {
        return url('storage/'.$this->path);
    }
}
