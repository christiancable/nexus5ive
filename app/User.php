<?php

namespace Nexus;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

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
    
    protected $dates = ['latestLogin'];
    
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
        });
    }

    /* related models */
    
    public function comments()
    {
        return $this->hasMany('Nexus\Comment', 'user_id', 'id')->orderBy('id', 'dec');
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

    /* mutators */

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = \Hash::make($value);
    }
    /* helper methods */

    public function incrementTotalPosts()
    {
        $this->totalPosts = $this->totalPosts + 1;
        $this->save();
    }

    /* returns true if the user has unread comments */
    public function hasNewComments()
    {
        $return = false;

        if (count($this->comments->where('read', false)->get('id')->take(1))) {
            $return = true;
        } else {
            $return = false;
        }

        return $return;
    }

    /* returns number of unread comments */
    public function newComments()
    {
        $return = \DB::table('comments')->select('id')->where('user_id', $this->id)->where('read', 0)->count();
        return $return;
    }

    public function markCommentsAsRead()
    {
        Comment::where('user_id', $this->id)->update(['read' => true]);
    }
}
