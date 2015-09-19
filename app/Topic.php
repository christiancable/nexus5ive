<?php

namespace Nexus;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $table = 'topictable';
    protected $primaryKey = 'topic_id';
    public $timestamps = false;

    /*
    accessors 
    =========

    covering up only database design stupids where I forgot about booleans
     */
    
    
    public function getReadOnlyAttribute()
    {
        $return = false;

        if ($this->topic_readonly === 'n') {
            $return = false;
        } else {
            $return = true;
        }

        return $return;
    }


    public function getSecretAttribute()
    {
        $return = false;

        if ($this->topic_annon === 'n') {
            $return = false;
        } else {
            $return = true;
        }

        return $return;
    }

    public function getMostRecentPostTimeAttribute()
    {
        $result = false;

        $latestPost =  Post::select('message_time')
            ->where('topic_id', $this->topic_id)
            ->orderBy('message_time', 'dec')
            ->first();

        if ($latestPost) {
            $result = $latestPost->message_time;
        }

        return $result;
    }

    public function mostRecentlyReadPostDate($user_id)
    {
        $result = false;

        $latestView = \Nexus\View::select('msg_date')
            ->where('topic_id', $this->topic_id)
            ->where('user_id', $user_id)
            ->first();

        if ($latestView) {
            $result = $latestView->msg_date;
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
        
        return $return;
    }

    // sections
     
    public function section()
    {
        return $this->belongsTo('Nexus\Section', 'section_id', 'section_id');
    }


    // posts
    
    public function posts()
    {
        return $this->hasMany('Nexus\Post', 'topic_id', 'topic_id')->orderBy('message_id', 'asc');
    }

    // views

    public function views()
    {
        return $this->hasMany('Nexus\View', 'topic_id', 'topic_id')->orderBy('msg_date', 'dec');
    }
}

/*
+--------------------+---------------+------+-----+---------+----------------+
| Field              | Type          | Null | Key | Default | Extra          |
+--------------------+---------------+------+-----+---------+----------------+
| topic_id           | int(11)       | NO   | PRI | NULL    | auto_increment |
| topic_title        | varchar(50)   | YES  |     | NULL    |                |
| section_id         | int(11)       | YES  | MUL | NULL    |                |
| topic_description  | mediumtext    | YES  |     | NULL    |                |
| topic_annon        | enum('y','n') | YES  |     | n       |                |
| topic_readonly     | enum('y','n') | YES  |     | n       |                |
| topic_weight       | tinyint(4)    | YES  |     | 10      |                |
| topic_title_hidden | enum('y','n') | YES  |     | n       |                |
+--------------------+---------------+------+-----+---------+----------------+
 */
