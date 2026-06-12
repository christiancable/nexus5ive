<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $casts = [
        'deleted_at' => 'datetime',
        'time' => 'datetime',
    ];

    protected $table = 'activities';

    protected $fillable = ['user_id', 'text', 'route', 'time'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
