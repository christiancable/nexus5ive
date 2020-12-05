<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Theme whereUpdatedAt($value)
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
