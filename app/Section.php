<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

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
            Log::info("Deleting Section $section->title - $section->id");
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
    * @return collection of all descendant sections
    */
    public function allChildSections()
    {
        $allChildSections = new \Illuminate\Support\Collection;
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
    
    // topics
    
    public function topics()
    {
        // return $this->hasMany('App\Topic', 'topic_id', 'id')->orderBy('topic_weight', 'asc');
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
        return $this->hasMany(\App\Topic::class)->onlyTrashed()->orderBy('weight', 'asc');
    }
    
    
    // posts
    
    public function getMostRecentPostAttribute()
    {
        $topicIDs = Topic::select('id')->where('section_id', $this->id)->get()->toArray();
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
        return str_slug($this->title, '-');
    }
}
