<?php

namespace Nexus;

use Illuminate\Database\Eloquent\Model;

class Mention extends Model
{
  
    public function post()
    {
        return $this->belongsTo('Nexus\Post');
    }

    public function user()
    {
        return $this->belongsTo('Nexus\User');
    }
}
