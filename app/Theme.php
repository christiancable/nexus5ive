<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Theme
 *
 * @property int $id
 * @property string $path
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read bool $external
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
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

    public function users()
    {
        return $this->hasMany(\App\User::class);
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
