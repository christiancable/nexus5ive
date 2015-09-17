<?php

namespace Nexus;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $table = 'topictable';
    protected $primaryKey = 'topic_id';
    public $timestamps = false;


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
