<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class View extends Model
{
    use SoftDeletes;

    /* dates */
    protected $dates = ['latest_view_date', 'deleted_at'];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function topic()
    {
        return $this->belongsTo(\App\Topic::class);
    }
}
