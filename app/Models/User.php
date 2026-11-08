<?php

namespace App\Models;

use App\Events\UserCreated;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * @property-read Collection<int, Post> $posts
 * @property-read Collection<int, Comment> $comments
 * @property-read Collection<int, Section> $sections
 * @property-read Collection<int, View> $views
 * @property-read Collection<int, Chat> $chats
 * @property-read Collection<int, Mention> $mentions
 * @property-read Activity $activity
 * @property-read Collection<int, Comment> $givenComments
 * @property-read Collection<int, Comment> $modifiedPosts
 * @property string $username
 * @property string $email
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
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
     * @var list<string>
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
            'is_guest' => 'bool',
            'private' => 'bool',
            'banned' => 'bool',
        ];
    }

    // below is copypasta of old
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'username';
    }

    public static function boot(): void
    {
        parent::boot();
        // Attach event handler, on deleting of the user
        User::deleting(function ($user) {
            Log::notice("Deleting user $user->username $user->id");

            // Clear the modified-by reference in bulk — no model events needed here
            Log::info('- resetting author from '.$user->modifiedPosts()->count().' modifiedPosts');
            $user->modifiedPosts()->update(['update_user_id' => null]);

            // Delete collections one record at a time so each model's own delete events fire
            // (e.g. Section::deleting cascades into topics). Bulk delete would skip those events.
            foreach (['posts', 'comments', 'sections', 'views', 'givenComments'] as $relation) {
                Log::info('- removing '.$user->$relation->count()." $relation");
                $user->$relation->each->delete();
            }

            // Activity is a single HasOne record, not a collection
            Log::info('- removing activity');
            $user->activity?->delete();
        });

        // log new users
        User::created(
            function ($user) {
                event(new UserCreated($user));
            }
        );
    }

    /* related models */

    public function mentions(): HasMany
    {
        return $this->hasMany(Mention::class)->orderBy('id', 'desc');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'user_id', 'id')->orderBy('id', 'desc');
    }

    public function givenComments(): HasMany
    {
        return $this->hasMany(Comment::class, 'author_id', 'id')->orderBy('id', 'desc');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(View::class)->orderBy('latest_view_date', 'desc');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function modifiedPosts(): HasMany
    {
        return $this->hasMany(Post::class, 'update_user_id', 'id');
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class, 'owner_id')->orderBy('updated_at', 'desc');
    }

    public function unreadChats(): HasMany
    {
        return $this->chats()->where('is_read', false)->orderBy('updated_at', 'desc');
    }

    public function activity(): HasOne
    {
        return $this->hasOne(Activity::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    /*
      returns collection of trashed topics
    */
    public function getTrashedTopicsAttribute(): Collection
    {
        // hasManyThrough cannot be used here because Topic uses SoftDeletes —
        // a standard relationship only returns non-trashed records and has no
        // onlyTrashed() equivalent. Querying via section IDs is the correct approach.
        $sectionIDs = $this->sections->pluck('id')->toArray();

        return Topic::onlyTrashed()
            ->whereIn('section_id', $sectionIDs)
            ->get();
    }
    /* helper methods */

    public function incrementTotalPosts(): void
    {
        $this->totalPosts = $this->totalPosts + 1;
        $this->save();
    }

    /* returns number of unread comments */
    public function newCommentCount(): int
    {
        return $this->comments()->where('read', false)->select('id')->count();
    }

    public function markCommentsAsRead(): void
    {
        Comment::where('user_id', $this->id)->update(['read' => true]);
    }

    public function clearComments(): void
    {
        $this->comments()->delete();
    }

    public function unreadChatCount(): int
    {
        return $this->chats()->where('is_read', false)->select('id')->count();
    }

    // dealing with @ mentions
    public function clearMentions(): void
    {
        $this->mentions()->delete();
    }

    public function addMention(Post $post): void
    {
        $mention = new Mention;
        $mention->user_id = $this->id;
        $mention->post_id = $post->id;
        $this->mentions()->save($mention);
    }

    public function removeMentions(array $posts): void
    {
        $this->mentions()->whereIn('post_id', Arr::pluck($posts, 'id'))->delete();
    }

    public function notificationCount(): int
    {
        $count = 0;
        $count = $count + $this->unreadChatCount();
        $count = $count + $this->newCommentCount();
        $count = $count + count($this->mentions);

        return $count;
    }

    /**
     * exclude unverified users
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('email_verified_at', '<>', null);
    }

    /**
     * exclude verified users
     */
    public function scopeUnverified(Builder $query): Builder
    {
        return $query->where('email_verified_at', '=', null);
    }

    public function isAdmin(): bool
    {
        return $this->administrator == true;
    }

    public function isGuest(): bool
    {
        return $this->is_guest === true;
    }
}
