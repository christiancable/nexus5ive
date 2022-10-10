<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Message
 *
 * @property int $id
 * @property int $user_id
 * @property int $author_id
 * @property string|null $text
 * @property bool $read
 * @property \Illuminate\Support\Carbon $time
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\User $author
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereUserId($value)
 * @mixin \Eloquent
 */
class Message extends Model
{
    protected $fillable = ['user_id','author_id','text','read', 'time'];

    protected $casts = [
        'read' => 'boolean',
        'time' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(\App\User::class, 'author_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
