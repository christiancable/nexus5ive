<?php

namespace App\Nexus;

use Illuminate\Database\Eloquent\Model;

class UserComment extends Model
{
    protected $table = 'commenttable';
    protected $primaryKey = 'comment_id';

    public function author()
    {
        return $this->hasOne('App\Nexus\User', 'user_id', 'from_id');
    }
}
