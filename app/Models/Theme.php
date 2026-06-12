<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * @return HasMany<User, $this>
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * GetUCNameAttribute
     * the name field in sentence case
     */
    public function getUCNameAttribute(): string
    {
        return ucwords($this->name);
    }

    /**
     * GetExternalAttribute
     * is the theme css internal or external
     */
    public function getExternalAttribute(): bool
    {
        return strpos($this->path, 'http') === 0;
    }
}
