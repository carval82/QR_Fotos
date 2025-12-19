<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'event_id',
        'sender_name',
        'message',
        'sender_phone',
        'sender_email',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
