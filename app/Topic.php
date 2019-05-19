<?php

namespace App;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use App\Events\TopicJumpCacheBecameDirty;
use Illuminate\Database\Eloquent\SoftDeletes;

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

        // forget the topicjump cache when a topic changes, is created or destroyed
        Topic::deleted(function () {event(new TopicJumpCacheBecameDirty());});
        Topic::updated(function () {event(new TopicJumpCacheBecameDirty());});
        Topic::created(function () {event(new TopicJumpCacheBecameDirty());});        
    }
    /**
     * returns the time of the most recent post
     * if the topic has no posts then return the created time of the topic
     *
     * @return Carbon|null
     */
    public function getMostRecentPostTimeAttribute()
    {
        $latestPost =  Post::select('time')
            ->where('topic_id', $this->id)
            ->orderBy('time', 'dec')
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
        return $this->hasOne(\App\Post::class)->latest();
    }
    
    // phpcs:disable PSR1.Methods.CamelCapsMethodName
    public function most_recent_post_id()
    {
    // phpcs:enable
        return $this->hasOne(\App\Post::class)->latest()->select(['id as post_id','topic_id']);
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

    // views

    public function views()
    {
        return $this->hasMany(\App\View::class)->orderBy('latest_view_date', 'dec');
    }
}
