<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['user_id', 'author_id', 'text', 'read', 'time'];

    protected $casts = [
        'read' => 'boolean',
        'time' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
