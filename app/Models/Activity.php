<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $casts = [
        'deleted_at' => 'datetime',
        'time' => 'datetime',
    ];

    protected $table = 'activities';

    protected $fillable = ['user_id', 'text', 'route', 'time'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
