<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\View
 *
 * @property int $id
 * @property int $user_id
 * @property int $topic_id
 * @property \Illuminate\Support\Carbon $latest_view_date
 * @property bool $unsubscribed
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Topic $topic
 * @property-read \App\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\View newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\View newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\View onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\View query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\View whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\View whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\View whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\View whereLatestViewDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\View whereTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\View whereUnsubscribed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\View whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\View whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\View withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\View withoutTrashed()
 * @mixin \Eloquent
 */
class View extends Model
{
    use SoftDeletes;

    /* dates */
    protected $dates = ['latest_view_date', 'deleted_at'];

    protected $casts = [
        'unsubscribed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function topic()
    {
        return $this->belongsTo(\App\Topic::class);
    }
}
