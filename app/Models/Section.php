<?php

namespace App\Models;

use App\Events\TreeCacheBecameDirty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Helpers\TreeHelper;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read \App\Models\Section|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Topic> $trashedTopics
 * @property-read \App\Models\User $moderator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Topic> $topics
 */
class Section extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    protected $fillable = ['id', 'title', 'intro', 'user_id', 'parent_id', 'weight'];

    public static function boot()
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

    public function moderator()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // sections

    public function parent()
    {
        return $this->belongsTo(Section::class, 'parent_id', 'id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'parent_id', 'id')->orderBy('weight', 'asc');
    }

    /**
     * @return Collection - all descendant sections
     */
    public function allChildSections()
    {
        $allChildSections = new Collection;
        foreach ($this->sections as $child) {
            $allChildSections->prepend($child);
            $allChildSections = self::listChildren($child, $allChildSections);
        }

        return $allChildSections;
    }

    private static function listChildren(Section $section, $children)
    {
        foreach ($section->sections as $child) {
            $children->prepend($child);
            $children = self::listChildren($child, $children);
        }

        return $children;
    }

    public function getIsHomeAttribute()
    {
        return $this->parent_id === null;
    }

    // topics
    public function topics()
    {
        return $this->hasMany(Topic::class)->orderBy('weight', 'asc');
    }

    public function trashedTopics()
    {
        return $this->topics()->onlyTrashed()->orderBy('weight', 'asc');
    }

    // posts

    public function getMostRecentPostAttribute()
    {
        $cacheKey = 'mostRecentPost'.$this->id;

        return Cache::rememberForever(
            $cacheKey,
            function () {
                return $this->recalculateMostRecentPost();
            }
        );
    }

    public static function forgetMostRecentPostAttribute($section_id = null)
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
    private function recalculateMostRecentPost()
    {
        $topicIDs = Topic::withoutGlobalScope('with_most_recent_post')->select('id')
            ->where('section_id', $this->id)->get()->toArray();
        if (count($topicIDs) == 0) {
            return null;
        }

        $postID = Post::select('id')->whereIn('topic_id', $topicIDs)->orderBy('id', 'desc')->first();
        if (! $postID) {
            return null;
        }

        return Post::find($postID->id);
    }

    public function slug()
    {
        return Str::slug($this->title, '-');
    }
}
