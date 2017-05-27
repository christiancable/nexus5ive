<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';
    protected $dates = ['time', 'deleted_at'];
    protected $fillable = ['user_id', 'text', 'route', 'time'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
