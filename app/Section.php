<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Events\TreeCacheBecameDirty;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Section
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $intro
 * @property int $user_id
 * @property int|null $parent_id
 * @property int $weight
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $is_home
 * @property-read mixed $most_recent_post
 * @property-read mixed $section_count
 * @property-read mixed $topic_count
 * @property-read \App\User $moderator
 * @property-read \App\Section|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Section[] $sections
 * @property-read int|null $sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Topic[] $topics
 * @property-read int|null $topics_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Section newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Section onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Section query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Section whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Section whereIntro($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Section whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Section whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Section whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Section whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Section whereWeight($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Section withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Section withoutTrashed()
 * @mixin \Eloquent
 */
class Section extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['id','title','intro','user_id','parent_id', 'weight'];
    protected $dates = ['deleted_at'];
    
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
            event(new TreeCacheBecameDirty());
        });
        Section::updated(function () {
            event(new TreeCacheBecameDirty());
        });
        Section::created(function () {
            event(new TreeCacheBecameDirty());
        });
    }
    
    // users
    
    public function moderator()
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }
    
    // sections
    
    public function parent()
    {
        return $this->belongsTo(\App\Section::class, 'parent_id', 'id');
    }
    
    public function sections()
    {
        return $this->hasMany(\App\Section::class, 'parent_id', 'id')->orderBy('weight', 'asc');
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
        return null === $this->parent_id;
    }

    // topics
    public function topics()
    {
        return $this->hasMany(\App\Topic::class)->orderBy('weight', 'asc');
    }
    
    // counts - hopefully faster ...
    public function getTopicCountAttribute()
    {
        return Topic::select(\DB::raw('count(id) as count'))->where('section_id', $this->id)->value('count');
    }
    
    public function getSectionCountAttribute()
    {
        return Section::select(\DB::raw('count(id) as count'))->where('parent_id', $this->id)->value('count');
    }
    
    public function trashedTopics()
    {
        return $this->topics()->onlyTrashed()->orderBy('weight', 'asc');
    }
    
    
    // posts
    
    public function getMostRecentPostAttribute()
    {
        $cacheKey = 'mostRecentPost' . $this->id;
        $section_id = $this->id;

        return Cache::rememberForever(
            $cacheKey,
            function () use ($section_id) {
                return self::recalculateMostRecentPost($section_id);
            }
        );
    }
        
    /**
     * recalculateMostRecentPost
     *
     * @param mixed $section_id - ID of the section
     * @return Post - the most recent post for the section or null
     */
    private static function recalculateMostRecentPost($section_id = null)
    {
        if (null === $section_id) {
            return null;
        }

        $topicIDs = Topic::select('id')->where('section_id', $section_id)->get()->toArray();
        if (0 == count($topicIDs)) {
            return null;
        }

        $postID = Post::select('id')->whereIn('topic_id', $topicIDs)->orderBy('id', 'desc')->get()->first();
        if (!$postID) {
            return null;
        }
        
        return Post::find($postID->id);
    }
    
    public function slug()
    {
        return Str::slug($this->title, '-');
    }
}
