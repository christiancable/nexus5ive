<?php

namespace Nexus;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'commenttable';
    protected $primaryKey = 'comment_id';
    public $timestamps = false;
    protected $fillable = ['user_id','from_id','text','readstatus'];

    public function author()
    {
        return $this->hasOne('Nexus\User', 'id', 'from_id');
    }

    public function user()
    {
        return $this->hasOne('Nexus\User', 'id', 'user_id');
    }

    /*
    accessors and mutators
    =======================

    covering up only database design stupids where I forgot about booleans
     */
    
    public function setReadstatusAttribute($value)
    {
        if ($value == true) {
            $this->attributes['readstatus'] = 'y';
        } else {
            $this->attributes['readstatus'] = "n";
        }
    }

    public function getReadstatusAttribute($value)
    {
        $return = false;

        if ($value === 'n') {
            $return = false;
        } else {
            $return = true;
        }

        return $return;
    }
}

/*
mysql> describe commenttable;
+------------+---------------+------+-----+---------+----------------+
| Field      | Type          | Null | Key | Default | Extra          |
+------------+---------------+------+-----+---------+----------------+
| comment_id | int(11)       | NO   | PRI | NULL    | auto_increment |
| user_id    | int(11)       | NO   | MUL | 0       |                |
| from_id    | int(11)       | NO   |     | 0       |                |
| text       | varchar(200)  | YES  |     | NULL    |                |
| readstatus | enum('y','n') | YES  |     | NULL    |                |
+------------+---------------+------+-----+---------+----------------+
 */