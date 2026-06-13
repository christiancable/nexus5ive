<?php

namespace App\Models;

/**
 * @property Topic $topic
 * @property Carbon|null $time
 */

use App\Events\MostRecentPostForSectionBecameDirty;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'deleted_at' => 'datetime',
        'time' => 'datetime',
    ];

    protected $fillable = ['title', 'text', 'time', 'popname', 'html', 'user_id', 'topic_id', 'update_user_id'];

    public static function boot(): void
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

    /**
     * @return BelongsTo<Topic, $this>
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function editor()
    {
        return $this->belongsTo(User::class, 'update_user_id');
    }

    /**
     * moderation reports for this post
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
