<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'token',
        'status',
        'requires_moderation',
    ];

    protected $casts = [
        'requires_moderation' => 'boolean',
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }
}
