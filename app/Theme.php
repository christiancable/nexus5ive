<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $table = 'themes';
    protected $fillable = [
        'name',
        'path',
        'created_at',
        'updated_at'
    ];

    public function users()
    {
        return $this->hasMany(\App\User::class);
    }
}
