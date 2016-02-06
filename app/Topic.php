<?php

namespace Nexus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

class Topic extends Model
{
    use SoftDeletes;

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
        Topic::deleting(function($topic) {
           
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

        return $result;
    }

    public function mostRecentlyReadPostDate($user_id)
    {
        $result = false;

        $latestView = \Nexus\View::select('latest_view_date')
            ->where('topic_id', $this->id)
            ->where('user_id', $user_id)
            ->first();

        if ($latestView) {
            $result = $latestView->latest_view_date;
        }

        return $result;
    }

    /**
     * reports if a topic has been updated since the a user last read
     *
     * this might actually live in the topicController
     * @param  int $user_id id of a user
     * @return boolean has the topic being updated or not
     */
    public function unreadPosts($user_id)
    {
        $return = true;

        $mostRecentlyReadPostDate = $this->mostRecentlyReadPostDate($user_id);

        if ($mostRecentlyReadPostDate) {
            if ($mostRecentlyReadPostDate <> $this->most_recent_post_time) {
                $return = true;
            } else {
                $return = false;
            }
        } else {
            $return = false;
        }
        
        if (!$this->most_recent_post_time) {
            $return = false;
        }

        return $return;
    }

    // sections
     
    public function section()
    {
        return $this->belongsTo('Nexus\Section');
    }


    // posts
    
    public function posts()
    {
        return $this->hasMany('Nexus\Post')->orderBy('id', 'asc');
    }

    // views

    public function views()
    {
        return $this->hasMany('Nexus\View')->orderBy('latest_view_date', 'dec');
    }
}
