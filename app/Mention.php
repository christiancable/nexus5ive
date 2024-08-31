<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Mention
 *
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property int $read
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Post $post
 * @property-read \App\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Mention newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Mention newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Mention query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Mention whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Mention whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Mention whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Mention wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Mention whereRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Mention whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Mention whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Mention extends Model
{
    public function post()
    {
        return $this->belongsTo(\App\Post::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
