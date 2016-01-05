<?php

namespace Nexus;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'name',
        'password',
        'email',
        'popname',
        'about',
        'location',
        'favouriteMovie',
        'favouriteMusic',
        'private'
    ];
     
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    
    /* dates */
    
    protected $dates = ['latestLogin','deleted_at'];
    
    public static function boot()
    {
        parent::boot();

        // Attach event handler, on deleting of the user
        User::deleting(function($user) {
            // for each post that the user has modified set the modified by user to null
            foreach ($user->modifiedPosts as $modifiedPost) {
                $modifiedPost->update_user_id = null;
                $modifiedPost->update();
            }

            /*
            to keep a cascading delete when using softDeletes we must remove the related models here
             */
            $children = ['comments', 'sections', 'views', 'messages', 'sentMessages', 'activity', 'givenComments'];
            Log::info("Deleting User $user->username - $user->id");
            foreach ($children as $child) {
                if ($user->$child()) {
                    // we need to call delete on the grandchilden to
                    // trigger their delete() events - seems dumb
                    if (get_class($user->$child) === 'Illuminate\Database\Eloquent\Collection') {
                        foreach ($user->$child as $grandchild) {
                            Log::info(" - removing grandchildren of user->{$child}");
                            $grandchild->delete();
                        }
                    } else {
                        Log::info(" - removing user->$child");
                        $user->$child->delete();
                    }
                }
            }
        });
    }

    /* related models */
    
    public function comments()
    {
        return $this->hasMany('Nexus\Comment', 'user_id', 'id')->orderBy('id', 'dec');
    }

    public function givenComments()
    {
        return $this->hasMany('Nexus\Comment', 'author_id', 'id')->orderBy('id', 'dec');
    }

    public function sections()
    {
        return $this->hasMany('Nexus\Section');
    }

    public function views()
    {
        return $this->hasMany('Nexus\View')->orderBy('latest_view_date', 'dec');
    }

    public function modifiedPosts()
    {
        return $this->hasMany('Nexus\Post', 'update_user_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany('Nexus\Message');
    }

    public function sentMessages()
    {
        return $this->hasMany('Nexus\Message', 'author_id');
    }

    public function activity()
    {
        return $this->hasOne('Nexus\Activity');
    }

      /* helper methods */

    public function incrementTotalPosts()
    {
        $this->totalPosts = $this->totalPosts + 1;
        $this->save();
    }

    /* returns number of unread comments */
    public function newCommentCount()
    {
        return $this->comments->where('read', false)->count();
    }

    public function markCommentsAsRead()
    {
        Comment::where('user_id', $this->id)->update(['read' => true]);
    }

    public function newMessageCount()
    {
        return $this->messages->where('read', false)->count();
    }
}
