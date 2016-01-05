<?php

namespace Nexus;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id','author_id','text','read'];

    protected $casts = [
        'read' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo('Nexus\User', 'author_id');
    }

    public function user()
    {
        return $this->belongsTo('Nexus\User', 'user_id');
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
