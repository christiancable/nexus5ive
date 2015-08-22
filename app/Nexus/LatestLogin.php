<?php

namespace App\Nexus;

use Illuminate\Database\Eloquent\Model;

class LatestLogin extends Model
{
    protected $table = 'whoison';
    protected $primaryKey = 'whoison_id';
    public $timestamps = false;

    protected $dates = ['timeon'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'user_id');
    }
}


/*
mysql> describe whoison;
+------------+-----------+------+-----+-------------------+-----------------------------+
| Field      | Type      | Null | Key | Default           | Extra                       |
+------------+-----------+------+-----+-------------------+-----------------------------+
| whoison_id | int(11)   | NO   | PRI | NULL              | auto_increment              |
| user_id    | int(11)   | YES  |     | NULL              |                             |
| timeon     | timestamp | NO   |     | CURRENT_TIMESTAMP | on update CURRENT_TIMESTAMP |
+------------+-----------+------+-----+-------------------+-----------------------------+
 */
