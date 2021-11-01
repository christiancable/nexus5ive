<?php

namespace App;

use App\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Events\MostRecentPostForSectionBecameDirty;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Post
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $text
 * @property \Illuminate\Support\Carbon $time
 * @property string|null $popname
 * @property int $html
 * @property int $user_id
 * @property int $topic_id
 * @property int|null $update_user_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\User $author
 * @property-read \App\User|null $editor
 * @property-read \App\Topic $topic
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Post onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post wherePopname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereUpdateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Post withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Post withoutTrashed()
 * @mixin \Eloquent
 */
class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['title','text','time','popname','html','user_id','topic_id','update_user_id'];
    protected $dates = ['time', 'deleted_at'];

    public static function boot()
    {
        parent::boot();

        // attach events for updated section->most_recent_post
        Post::deleting(function ($post) {
            if ($post->id === $post->topic->section->most_recent_post->id) {
                event(new MostRecentPostForSectionBecameDirty($post->topic->section_id));
            }
        });

        Post::created(function ($post) {
            event(new MostRecentPostForSectionBecameDirty($post->topic->section_id));
        });
    }

    public function topic()
    {
        return $this->belongsTo(\App\Topic::class);
    }

    public function author()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function editor()
    {
        return $this->belongsTo(\App\User::class, 'update_user_id');
    }
}
