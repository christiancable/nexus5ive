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

    /**
     * getExternalAttribute
     * - is the theme css internal or external
     * @return bool
     */
    public function getExternalAttribute()
    {
        return 0 === strpos($this->path, 'http');
    }
}
