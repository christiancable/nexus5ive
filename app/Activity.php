<?php

namespace Nexus;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';
    protected $dates = ['time'];
    protected $fillable = ['user_id', 'text', 'route', 'time'];

    public function user()
    {
        return $this->belongsTo('Nexus\User');
    }
}