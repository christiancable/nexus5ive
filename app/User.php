<?php

namespace App;

use Log;
use App\ViewModels\UserPresenter;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Notifiable;
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
        'private',
        'viewLatestPostFirst',
        'theme_id'
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
        User::deleting(function ($user) {
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
                        if ($user->child) {
                            $user->$child->delete();
                        }
                    }
                }
            }
        });
    }

    /* related models */

    public function mentions()
    {
        return $this->hasMany(\App\Mention::class)->orderBy('id', 'dec');
    }
    
    public function comments()
    {
        return $this->hasMany(\App\Comment::class, 'user_id', 'id')->orderBy('id', 'dec');
    }

    public function givenComments()
    {
        return $this->hasMany(\App\Comment::class, 'author_id', 'id')->orderBy('id', 'dec');
    }

    public function sections()
    {
        return $this->hasMany(\App\Section::class);
    }

    public function views()
    {
        return $this->hasMany(\App\View::class)->orderBy('latest_view_date', 'dec');
    }

    public function modifiedPosts()
    {
        return $this->hasMany(\App\Post::class, 'update_user_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany(\App\Message::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(\App\Message::class, 'author_id');
    }

    public function activity()
    {
        return $this->hasOne(\App\Activity::class);
    }
    
    public function theme()
    {
        return $this->belongsTo(\App\Theme::class);
    }

    /*
      returns collection of trashed topics
    */
    public function getTrashedTopicsAttribute()
    {
        /*
            @todo: why does the hasManyThrough not work here?
            return $this->hasManyThrough('App\Topic', 'App\Section', 'user_id', 'section_id');
        */

        $sectionIDs = $this->sections->pluck('id')->toArray();
        
        $trashedTopics = Topic::onlyTrashed()
            ->whereIn('section_id', $sectionIDs)
            ->get();
        
        return $trashedTopics;
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
        return $this->comments(['id','read'])->where('read', false)->count();
    }

    public function markCommentsAsRead()
    {
        Comment::where('user_id', $this->id)->update(['read' => true]);
    }

    public function clearComments()
    {
        $this->comments()->delete();
    }

    public function newMessageCount()
    {
        return $this->messages(['id', 'read'])->where('read', false)->count();
    }

    // dealing with @ mentions
    public function clearMentions()
    {
        $this->mentions()->delete();
    }

    public function addMention(\App\Post $post)
    {
        $mention = new \App\Mention;
        $mention->user_id = $this->id;
        $mention->post_id = $post->id;
        $this->mentions()->save($mention);
    }

    public function removeMentions(array $posts)
    {
        $this->mentions()->whereIn('post_id', array_pluck($posts, 'id'))->delete();
    }

    public function notificationCount()
    {
        $count = 0;
        $count = $count + $this->newMessageCount();
        $count = $count + $this->newCommentCount();
        $count = $count + count($this->Mentions);

        return $count;
    }

       /**
     * Present the user model.
     *
     * @return ViewModels\UserPresenter
     */
    public function present()
    {
        return new UserPresenter($this);
    }
}
