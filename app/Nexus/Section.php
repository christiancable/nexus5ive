<?php

namespace App\Nexus;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
     protected $table = 'sectiontable';
     protected $primaryKey = 'section_id';
    
    // public function owner()
    // {
    //     return $this->hasOne('App\Nexus\User', 'user_id', 'user_id');
    // }

    // public function parent()
    // {
    //     return $this->hasOne('App\Nexus\Section', 'section_id', 'parent_id');
    // }
}

/*


 */
