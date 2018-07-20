<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id','author_id','text','read'];

    protected $casts = [
        'read' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(\App\User::class, 'author_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }
}
