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
        return $this->hasMany('App\Nexus\Comment', 'user_id', 'user_id')->orderBy('comment_id', 'dec');
    }

    public function sections()
    {
        return $this->hasMany('App\Nexus\Section');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'nexus_id', 'user_id');
    }

    
    /* non-database attributes */

    public function getLastSeenAttribute()
    {
        if ($time = $this->latestLogin) {
            return $time->timeon->diffForHumans();
        } else {
            return 'Never';
        }
    }

    
    /* relationsips to other models */

    public function latestLogin()
    {
        return $this->hasOne('App\Nexus\LatestLogin', 'user_id', 'user_id');
    }
}
