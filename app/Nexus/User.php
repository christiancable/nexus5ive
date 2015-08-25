<?php

namespace App\Nexus;

use Carbon\Carbon;

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


    public function getSysopAttribute()
    {
        if ($this->user_sysop === 'y') {
            return true;
        } else {
            return false;
        }
    }

    // public function getLatestLoginAttribute()
    // {
    //     return $this->latestLogin;
    // }
    
    /* query scopes */

    // public function scopeRecent($query)
    // {
    //     return $query->where('$this->latestLogin->timeon', '>=', Carbon::now()->subWeek());
    // }

    public function getOnlineAttribute()
    {
        return false;
    }

    /* relationsips to other models */

    public function latestLogin()
    {
        return $this->hasOne('App\Nexus\LatestLogin', 'user_id', 'user_id');
    }
}
