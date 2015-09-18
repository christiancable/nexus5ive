<?php

namespace Nexus;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $table = 'topicview';
    protected $primaryKey = 'topicview_id';
    public $timestamps = false;

    /* dates */
    
    protected $dates = ['msg_date'];

    public function user()
    {
        return $this->belongsTo('Nexus\User', 'id', 'user_id');
    }

    public function topic()
    {
        return $this->belongsTo('Nexus\Topic', 'topic_id', 'topic_id');
    }
}
/*
mysql> describe topicview;
+--------------+------------+------+-----+-------------------+-----------------------------+
| Field        | Type       | Null | Key | Default           | Extra                       |
+--------------+------------+------+-----+-------------------+-----------------------------+
| topicview_id | int(11)    | NO   | PRI | NULL              | auto_increment              |
| user_id      | int(11)    | YES  | MUL | NULL              |                             |
| topic_id     | int(11)    | YES  |     | NULL              |                             |
| msg_date     | timestamp  | NO   | MUL | CURRENT_TIMESTAMP | on update CURRENT_TIMESTAMP |
| unsubscribe  | tinyint(1) | YES  |     | 0                 |                             |
+--------------+------------+------+-----+-------------------+-----------------------------+
 */
