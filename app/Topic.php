<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

class Topic extends Model
{
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
            Log::info("Deleting Topic $topic->title - $topic->id");
            foreach ($children as $child) {
                if ($topic->$child()) {
                        Log::info(" - removing topic->$child");
                        $topic->$child()->delete();
                }
            }
        });
    }
    public function getMostRecentPostTimeAttribute()
    {
         $result = false;

        $latestPost =  Post::select('time')
            ->where('topic_id', $this->id)
            ->orderBy('time', 'dec')
            ->first();

        if ($latestPost) {
            $result = $latestPost->time;
        }

//         $result = $this->most_recent_post->time;
        return $result;
    }

    public function most_recent_post()
    {
        return $this->hasOne('App\Post')->latest();
    }
    
    public function most_recent_post_id()
    {
        return $this->hasOne('App\Post')->latest()->select(['id as post_id','topic_id']);
    }
    // sections
     
    public function section()
    {
        return $this->belongsTo('App\Section');
    }


    // posts
    
    public function posts()
    {
        return $this->hasMany('App\Post')->orderBy('id', 'asc');
    }

    // views

    public function views()
    {
        return $this->hasMany('App\View')->orderBy('latest_view_date', 'dec');
    }
}
