<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

use App\Events\UserCreated;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
        'theme_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'latestLogin' => 'datetime',
            'deleted_at' => 'datetime',
            'administrator' => 'bool',
            'private' => 'bool',
            'banned' => 'bool',
        ];
    }

    // below is copypasta of old
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'username';
    }

    public static function boot()
    {
        parent::boot();
        // Attach event handler, on deleting of the user
        User::deleting(
            function ($user) {
                Log::notice("Deleting user $user->username $user->id");
                // for each post that the user has modified set the modified by user to null
                Log::info('- resetting author from '.$user->modifiedPosts->count().' modifiedPosts');
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
                            Log::info('- removing '.$user->$child->count()." $child");
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
            }
        );

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
        return $this->hasMany(Mention::class)->orderBy('id', 'desc');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id')->orderBy('id', 'desc');
    }

    public function givenComments()
    {
        return $this->hasMany(Comment::class, 'author_id', 'id')->orderBy('id', 'desc');
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function views()
    {
        return $this->hasMany(View::class)->orderBy('latest_view_date', 'desc');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function modifiedPosts()
    {
        return $this->hasMany(Post::class, 'update_user_id', 'id');
    }

    // @todo remove
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // @todo remove
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'author_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'owner_id');
    }

    public function activity()
    {
        return $this->hasOne(Activity::class);
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class);
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
        return $this->comments()->where('read', false)->select('id')->count();
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
        return $this->messages()->where('read', false)->select('id')->count();
    }

    // dealing with @ mentions
    public function clearMentions()
    {
        $this->mentions()->delete();
    }

    public function addMention(Post $post)
    {
        $mention = new Mention;
        $mention->user_id = $this->id;
        $mention->post_id = $post->id;
        $this->mentions()->save($mention);
    }

    public function removeMentions(array $posts)
    {
        $this->mentions()->whereIn('post_id', Arr::pluck($posts, 'id'))->delete();
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
     * @return UserPresenter
     */
    public function present()
    {
        return new UserPresenter($this);
    }

    /**
     * exclude unverified users
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerified($query)
    {
        return $query->where('email_verified_at', '<>', null);
    }

    /**
     * exclude verified users
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnverified($query)
    {
        return $query->where('email_verified_at', '=', null);
    }

    public function isAdmin(): bool
    {
        return $this->administrator == true;
    }
}
