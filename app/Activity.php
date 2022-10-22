<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Activity
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $text
 * @property string|null $route
 * @property \Illuminate\Support\Carbon $time
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereUserId($value)
 * @mixin \Eloquent
 */
class Activity extends Model
{
    protected $casts = [
        'deleted_at' => 'datetime',
        'time'       => 'datetime',
    ];

    protected $table = 'activities';
    protected $fillable = ['user_id', 'text', 'route', 'time'];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
