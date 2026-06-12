<?php

namespace App\Models;

use App\Helpers\TreeHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Section> $sections
 * @property-read Section|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Topic> $trashedTopics
 * @property-read User $moderator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Topic> $topics
 */
class Section extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'deleted_at' => 'datetime',
        'allow_user_topics' => 'boolean',
    ];

    protected $fillable = ['id', 'title', 'intro', 'user_id', 'parent_id', 'weight', 'allow_user_topics'];

    public static function boot(): void
    {
        parent::boot();

        // Attach event handler for deleting a section
        Section::deleting(function ($section) {

            /*
            to keep a cascading delete when using softDeletes we must remove the related models here
            */
            $children = ['sections', 'topics'];
            Log::notice("Deleting Section $section->title - $section->id");
            foreach ($children as $child) {
                if ($section->$child()) {
                    Log::info(" - removing section->$child");
                    // we need to call delete on the grandchilden to
                    // trigger their delete() events - seems dumb
                    foreach ($section->$child as $grandchild) {
                        $grandchild->delete();
                    }
                }
            }
        });

        Section::deleted(function () {
            TreeHelper::rebuild();
        });
        Section::updated(function () {
            TreeHelper::rebuild();
        });
        Section::created(function () {
            TreeHelper::rebuild();
        });
    }

    // users

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // sections

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'parent_id', 'id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'parent_id', 'id')->orderBy('weight', 'asc');
    }

    /**
     * @return Collection - all descendant sections
     */
    public function allChildSections(): Collection
    {
        $allChildSections = new Collection;
        foreach ($this->sections as $child) {
            $allChildSections->prepend($child);
            $allChildSections = self::listChildren($child, $allChildSections);
        }

        return $allChildSections;
    }

    private static function listChildren(Section $section, Collection $children): Collection
    {
        foreach ($section->sections as $child) {
            $children->prepend($child);
            $children = self::listChildren($child, $children);
        }

        return $children;
    }

    public function getIsHomeAttribute(): bool
    {
        return $this->parent_id === null;
    }

    // topics
    /**
     * @return HasMany<Topic, $this>
     */
    public function topics(): HasMany
    {
        $query = $this->hasMany(Topic::class);

        if ($this->allow_user_topics) {
            // Sticky topics appear first, then order by most recent post time
            // Use COALESCE to fall back to topic created_at when no posts exist
            $query->orderByDesc('sticky');
            $query->orderByRaw(
                'COALESCE((SELECT time FROM posts WHERE topic_id = topics.id ORDER BY time DESC LIMIT 1), topics.created_at) DESC'
            );
        } else {
            // Moderator-controlled sections: order by weight only, sticky has no effect
            $query->orderBy('weight', 'asc');
        }

        return $query;
    }

    /**
     * @return HasMany<Topic, $this>
     */
    public function trashedTopics(): HasMany
    {
        return $this->topics()->onlyTrashed();
    }

    // posts

    public function getMostRecentPostAttribute(): ?Post
    {
        $cacheKey = 'mostRecentPost'.$this->id;

        return Cache::rememberForever(
            $cacheKey,
            function () {
                return $this->recalculateMostRecentPost();
            }
        );
    }

    public static function forgetMostRecentPostAttribute(?int $section_id = null): void
    {
        $cacheKey = 'mostRecentPost'.$section_id;
        Cache::forget($cacheKey);
    }

    /**
     * recalculateMostRecentPost
     *
     * @todo rewrite this logic to be more like the topic scope
     *
     * @return Post|null - the most recent post for the section or null
     */
    private function recalculateMostRecentPost(): ?Post
    {
        if ($this->topics->isEmpty()) {
            return null;
        }

        // Directly find the latest post within the section's topics
        $latestPost = Post::whereHas('topic', function ($query) {
            $query->where('section_id', $this->id);
        })
            ->orderBy('id', 'desc')
            ->first();

        return $latestPost;
    }

    public function slug(): string
    {
        return Str::slug($this->title, '-');
    }
}
