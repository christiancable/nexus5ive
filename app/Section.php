<?php

namespace Nexus;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['id','title','intro','user_id','parent_id', 'weight'];

    // users
    
    public function moderator()
    {
        return $this->hasOne('Nexus\User', 'id', 'user_id');
    }

    // sections

    public function parent()
    {
        return $this->belongsTo('Nexus\Section', 'parent_id', 'id');
    }

    public function sections()
    {
        return $this->hasMany('Nexus\Section', 'parent_id', 'id')->orderBy('weight', 'asc');
    }

    // topics
    
    public function topics()
    {
        // return $this->hasMany('Nexus\Topic', 'topic_id', 'id')->orderBy('topic_weight', 'asc');
        return $this->hasMany('Nexus\Topic')->orderBy('topic_weight', 'asc');
    }

    public function slug()
    {
        return str_slug($this->title, '-');
    }
}
