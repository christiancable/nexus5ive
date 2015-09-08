<?php

namespace App\Nexus;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'usertable';
    protected $primaryKey = 'user_id';
    public $timestamps = false;
    

}
