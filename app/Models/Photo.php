<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $appends = [
        'url',
    ];

    protected $fillable = [
        'event_id',
        'path',
        'original_name',
        'mime',
        'size',
        'status',
        'uploaded_ip',
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
