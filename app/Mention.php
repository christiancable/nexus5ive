<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mention extends Model
{
  
    public function post()
    {
        return $this->belongsTo(\App\Post::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
