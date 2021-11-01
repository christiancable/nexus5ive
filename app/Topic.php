<?php

namespace App;

use App\Post;
use App\Section;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Events\TreeCacheBecameDirty;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Events\MostRecentPostForSectionBecameDirty;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Topic
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $intro
 * @property int $section_id
 * @property int $secret
 * @property int $readonly
 * @property int $weight
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Support\Carbon|null $most_recent_post_time
 * @property-read \App\Post $most_recent_post
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $posts
 * @property-read int|null $posts_count
 * @property-read \App\Section $section
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\View[] $views
 * @property-read int|null $views_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Topic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Topic newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Topic onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Topic query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Topic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Topic whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Topic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Topic whereIntro($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Topic whereReadonly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Topic whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Topic whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Topic whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Topic whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Topic whereWeight($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Topic withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Topic withoutTrashed()
 * @mixin \Eloquent
 */
class Topic extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'title',
        'intro',
        'secret',
        'readonly',
        'weight',
        'section_id'
    ];

    public static function boot()
    {
        parent::boot();

        // Attach event handler for deleting a topic
        Topic::deleting(function ($topic) {

            /*
            to keep a cascading delete when using softDeletes we must remove the related models here
             */
            $children = ['posts', 'views'];
            Log::notice("Deleting Topic $topic->title - $topic->id");
            foreach ($children as $child) {
                if ($topic->$child()) {
                        Log::info(" - removing topic->$child");
                        $topic->$child()->delete();
                }
            }
        });

        // forget the tree cache when a topic changes, is created or destroyed
        Topic::deleted(function () {
            event(new TreeCacheBecameDirty());
        });
        Topic::updated(function ($topic) {
            $original_section_id = $topic->getOriginal('section_id');
            event(new MostRecentPostForSectionBecameDirty($original_section_id));
            event(new TreeCacheBecameDirty());
        });
        Topic::created(function () {
            event(new TreeCacheBecameDirty());
        });

        // add scope for most recent post
        static::addGlobalScope('with_most_recent_post', function ($query) {
            $query->addSelect(['most_recent_post_id' => Post::select('id')
            ->whereColumn('topic_id', 'topics.id')
            ->latest()
            ->take(1)
            ]);
        });
    }


    /**
     * returns the time of the most recent post
     * if the topic has no posts then return the created time of the topic
     *
     * @return Carbon|null
     */
    public function getMostRecentPostTimeAttribute()
    {

        $latestPost = Post::select('time')
            ->where('topic_id', $this->id)
            ->orderBy('time', 'desc')
            ->first();


        if ($latestPost) {
            $result = $latestPost->time;
        } else {
            $result = $this->created_at;
        }

        return $result;
    }

    // phpcs:disable PSR1.Methods.CamelCapsMethodName
    public function most_recent_post()
    {
         // phpcs:enable
        return $this->belongsTo(Post::class);
    }

    // sections

    public function section()
    {
        return $this->belongsTo(\App\Section::class);
    }


    // posts
    public function posts()
    {
        return $this->hasMany(\App\Post::class)->orderBy('id', 'asc');
    }

    // posts but in reverse order
    public function reversedPosts()
    {
        return $this->hasMany(\App\Post::class)->orderBy('id', 'desc');
    }
    // views

    public function views()
    {
        return $this->hasMany(\App\View::class)->orderBy('latest_view_date', 'desc');
    }
}
