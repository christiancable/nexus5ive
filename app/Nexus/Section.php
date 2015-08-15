<?php

namespace App\Nexus;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'sectiontable';
    protected $primaryKey = 'section_id';

    public $timestamps = false;

    public function moderator()
    {
        return $this->hasOne('App\Nexus\User', 'user_id', 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Nexus\Section', 'parent_id', 'section_id');
    }

    public function childen()
    {
        return $this->hasMany('App\Nexus\Section', 'section_id', 'parent_id')->orderBy('section_weight', 'asc');
    }

    public function slug()
    {
        return str_slug($this->section_title, '-');
    }
}

/*

DROP TABLE IF EXISTS `sectiontable`;
CREATE TABLE `sectiontable` (  
`section_id` int(11) NOT NULL AUTO_INCREMENT,
`section_title` varchar(50) DEFAULT NULL,
`user_id` int(11) DEFAULT NULL,
`parent_id` int(11) DEFAULT NULL, 
`section_weight` int(11) NOT NULL DEFAULT '0',
`section_intro` varchar(100) DEFAULT '',
PRIMARY KEY (`section_id`),
KEY `index_parent_id` (`parent_id`),  
KEY `id_weight` (`section_id`,`section_weight`)) 
)
 */
