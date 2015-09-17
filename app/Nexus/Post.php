<?php
namespace nexus\Nexus;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'messagetable';
    protected $primaryKey = 'message_id';
    protected $dates = ['message_time'];

    public $timestamps = false;

    protected $fillable = ['topic_id','user_id','message_title','message_text', 'message_popname'];

    // topic
    
    public function topic()
    {
        return $this->belongsTo('nexus\Nexus\Topic', 'topic_id', 'topic_id');
    }

    // users
    
    public function author()
    {
        return $this->belongsTo('nexus\User', 'user_id', 'id');
    }
}

/*
mysql> describe messagetable;
+-----------------+-------------+------+-----+-------------------+-----------------------------+
| Field           | Type        | Null | Key | Default           | Extra                       |
+-----------------+-------------+------+-----+-------------------+-----------------------------+
| message_id      | int(11)     | NO   | PRI | NULL              | auto_increment              |
| message_text    | mediumtext  | YES  |     | NULL              |                             |
| topic_id        | int(11)     | NO   | MUL | 0                 |                             |
| user_id         | int(11)     | NO   |     | 0                 |                             |
| message_title   | varchar(50) | YES  |     | NULL              |                             |
| message_time    | timestamp   | NO   | MUL | CURRENT_TIMESTAMP | on update CURRENT_TIMESTAMP |
| message_popname | varchar(70) | YES  |     | NULL              |                             |
| message_html    | tinyint(1)  | YES  |     | 0                 |                             |
| update_user_id  | int(11)     | YES  |     | 0                 |                             |
+-----------------+-------------+------+-----+-------------------+-----------------------------+
9 rows in set (0.00 sec)
 */
