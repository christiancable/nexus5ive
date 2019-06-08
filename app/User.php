<?php

namespace App;

use App\Events\UserCreated;
use App\ViewModels\UserPresenter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    // use Authenticatable, Authorizable, CanResetPassword;
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /* dates */
    
    protected $dates = ['latestLogin','deleted_at'];
    
    public static function boot()
    {
        parent::boot();
        // Attach event handler, on deleting of the user
        User::deleting(function ($user) {
            Log::notice("Deleting user $user->username $user->id");
            // for each post that the user has modified set the modified by user to null
            Log::info("- resetting author from " . $user->modifiedPosts->count() . " modifiedPosts");
            foreach ($user->modifiedPosts as $modifiedPost) {
                $modifiedPost->update_user_id = null;
                $modifiedPost->update();
            }

            /*
            to keep a cascading delete when using softDeletes we must remove the related models here
             */
            $children = ['posts',
                'comments',
                'sections',
                'views',
                'messages',
                'sentMessages',
                'activity',
                'givenComments'];
            foreach ($children as $child) {
                if ($user->$child !== null) {
                    // we need to call delete on the grandchilden to trigger their delete() events
                    if (get_class($user->$child) === 'Illuminate\Database\Eloquent\Collection') {
                        Log::info("- removing " . $user->$child->count() . " $child");
                        foreach ($user->$child as $grandchild) {
                            $grandchild->delete();
                        }
                    } else {
                        Log::info("- removing $child ");
                        if ($user->$child) {
                            $user->$child->delete();
                        }
                    }
                }
            }
        });

        // log new users
        User::created(
            function ($user) {
                event(new UserCreated($user));
            }
        );
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

    public function posts()
    {
        return $this->hasMany(\App\Post::class);
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
