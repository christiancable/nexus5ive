<?php

namespace App\Nexus;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'usertable';
    protected $primaryKey = 'user_id';
    public $timestamps = false;
    
    public function comments()
    {
        return $this->hasMany('App\Nexus\UserComment', 'user_id', 'user_id')->orderBy('comment_id');
    }

    public function sections()
    {
        return $this->hasMany('App\Nexus\Section');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'nexus_id', 'user_id');
    }
}
