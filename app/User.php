<?php

namespace App;

use App\Events\UserCreated;
use App\ViewModels\UserPresenter;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * App\User
 *
 * @codingStandardsIgnoreStart
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string $username
 * @property string|null $popname
 * @property string|null $about
 * @property string|null $location
 * @property bool $administrator
 * @property bool $banned
 * @property int $deleted
 * @property int $totalVisits
 * @property int $totalPosts
 * @property string|null $favouriteMovie
 * @property string|null $favouriteMusic
 * @property bool $private
 * @property string|null $ipaddress
 * @property string|null $currentActivity
 * @property \Illuminate\Support\Carbon|null $latestLogin
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $viewLatestPostFirst
 * @property int $theme_id
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property-read \App\Activity $activity
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read mixed $trashed_topics
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $givenComments
 * @property-read int|null $given_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Mention[] $mentions
 * @property-read int|null $mentions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Message[] $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $modifiedPosts
 * @property-read int|null $modified_posts_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Section[] $sections
 * @property-read int|null $sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Message[] $sentMessages
 * @property-read int|null $sent_messages_count
 * @property-read \App\Theme $theme
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\View[] $views
 * @property-read int|null $views_count
 *
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User unverified()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User verified()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAdministrator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCurrentActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereFavouriteMovie($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereFavouriteMusic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereIpaddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLatestLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePopname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereThemeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereTotalPosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereTotalVisits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereViewLatestPostFirst($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\User withoutTrashed()
 *
 * @mixin         \Eloquent
 *
 * @codingStandardsIgnoreEnd
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
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
        'theme_id',
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
        'latestLogin' => 'datetime',
        'deleted_at' => 'datetime',
        'administrator' => 'bool',
        'private' => 'bool',
        'banned' => 'bool',
    ];

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
        return $this->hasMany(\App\Mention::class)->orderBy('id', 'desc');
    }

    public function comments()
    {
        return $this->hasMany(\App\Comment::class, 'user_id', 'id')->orderBy('id', 'desc');
    }

    public function givenComments()
    {
        return $this->hasMany(\App\Comment::class, 'author_id', 'id')->orderBy('id', 'desc');
    }

    public function sections()
    {
        return $this->hasMany(\App\Section::class);
    }

    public function views()
    {
        return $this->hasMany(\App\View::class)->orderBy('latest_view_date', 'desc');
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

    public function addMention(\App\Post $post)
    {
        $mention = new \App\Mention;
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
     * @return ViewModels\UserPresenter
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
}
