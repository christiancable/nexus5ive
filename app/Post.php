<?php
namespace Nexus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['title','text','time','popname','html','user_id','topic_id','update_user_id'];
    protected $dates = ['time', 'deleted_at'];
    
    public function topic()
    {
        return $this->belongsTo('Nexus\Topic');
    }
    
    public function author()
    {
        return $this->belongsTo('Nexus\User', 'user_id');
    }

    public function editor()
    {
        return $this->belongsTo('Nexus\User', 'update_user_id');
    }
}
