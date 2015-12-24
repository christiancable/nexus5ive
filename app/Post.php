<?php
namespace Nexus;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title','text','time','popname','html','user_id','topic_id','update_user_id'];
    protected $dates = ['time'];
    
    public function topic()
    {
        return $this->belongsTo('Nexus\Topic');
    }
    
    public function author()
    {
        return $this->belongsTo('Nexus\User', 'user_id');
    }
}
