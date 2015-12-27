<?php

namespace Nexus;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    /* dates */
    protected $dates = ['latest_view_date'];

    public function user()
    {
        return $this->belongsTo('Nexus\User');
    }

    public function topic()
    {
        return $this->belongsTo('Nexus\Topic');
    }
}

