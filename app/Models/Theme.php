<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $table = 'themes';

    protected $fillable = [
        'name',
        'path',
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<User, $this>
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * GetUCNameAttribute
     * the name field in sentence case
     *
     * @return string
     */
    public function getUCNameAttribute()
    {
        return ucwords($this->name);
    }

    /**
     * GetExternalAttribute
     * is the theme css internal or external
     *
     * @return bool
     */
    public function getExternalAttribute()
    {
        return strpos($this->path, 'http') === 0;
    }
}
