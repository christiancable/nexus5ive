<?php

namespace App\Models;


use App\Events\MostRecentPostForSectionBecameDirty;
use App\Events\TreeCacheBecameDirty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * @property-read \App\Models\Section $section
 * @property-read \App\Models\Post|null $most_recent_post
 */
class Topic extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'title',
        'intro',
        'secret',
        'readonly',
        'weight',
        'section_id',
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
            event(new TreeCacheBecameDirty);
        });
        Topic::updated(function ($topic) {
            $original_section_id = $topic->getOriginal('section_id');
            event(new MostRecentPostForSectionBecameDirty($original_section_id));
            event(new TreeCacheBecameDirty);
        });
        Topic::created(function () {
            event(new TreeCacheBecameDirty);
        });

        // add scope for most recent post
        static::addGlobalScope('with_most_recent_post', function ($query) {
            $query->addSelect(['most_recent_post_id' => Post::select('id')
                ->whereColumn('topic_id', 'topics.id')
                ->latest()
                ->take(1),
            ]);
        });
    }

    /**
     * returns the time of the most recent post
     * if the topic has no posts then return the created time of the topic
     *
     * @return \Illuminate\Support\Carbon|null
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
        return $this->belongsTo(Section::class);
    }

    // posts
    public function posts()
    {
        return $this->hasMany(Post::class)->orderBy('id', 'asc');
    }

    // posts but in reverse order
    public function reversedPosts()
    {
        return $this->hasMany(Post::class)->orderBy('id', 'desc');
    }
    // views

    public function views()
    {
        return $this->hasMany(View::class)->orderBy('latest_view_date', 'desc');
    }
}
