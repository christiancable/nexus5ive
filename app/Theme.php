<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $table = 'themes';
    protected $fillable = [
        'name',
        'path'
    ];

    public function users()
    {
        return $this->hasMany('App\User');
    }
}
