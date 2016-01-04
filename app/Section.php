<?php

namespace Nexus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

class Section extends Model
{
    use SoftDeletes;

    protected $fillable = ['id','title','intro','user_id','parent_id', 'weight'];

    public static function boot()
    {
        parent::boot();

        // Attach event handler for deleting a section
        Section::deleting(function($section) {
           
            /*
            to keep a cascading delete when using softDeletes we must remove the related models here
             */
            $children = ['sections', 'topics'];
            Log::info("Deleting Section " . $section->id);
            foreach ($children as $child) {
                if ($section->$child()) {
                        Log::info(" -   removing section->$child");
                        $section->$child()->delete();
                }
            }
        });
    }

    // users
    
    public function moderator()
    {
        return $this->belongsTo('Nexus\User', 'user_id', 'id');
    }

    // sections

    public function parent()
    {
        return $this->belongsTo('Nexus\Section', 'parent_id', 'id');
    }

    public function sections()
    {
        return $this->hasMany('Nexus\Section', 'parent_id', 'id')->orderBy('weight', 'asc');
    }

    // topics
    
    public function topics()
    {
        // return $this->hasMany('Nexus\Topic', 'topic_id', 'id')->orderBy('topic_weight', 'asc');
        return $this->hasMany('Nexus\Topic')->orderBy('weight', 'asc');
    }

    public function slug()
    {
        return str_slug($this->title, '-');
    }
}
