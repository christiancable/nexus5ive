<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['user_id','author_id','text','read', 'time'];

    protected $casts = [
        'read' => 'boolean',
    ];

    protected $dates = ['time'];

    public function author()
    {
        return $this->belongsTo('App\User', 'author_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
