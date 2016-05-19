<?php

namespace Nexus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class View extends Model
{
    use SoftDeletes;

    /* dates */
    protected $dates = ['latest_view_date', 'deleted_at'];

    public function user()
    {
        return $this->belongsTo('Nexus\User');
    }

    public function topic()
    {
        return $this->belongsTo('Nexus\Topic');
    }
}
