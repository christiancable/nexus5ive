# User

The central model. Represents everyone who has an account on the BBS — active members, imported legacy users, and the shared guest account.

## Table: `users`

| Column | Type | Notes |
|--------|------|-------|
| `id` | unsigned int | Primary key |
| `username` | string | Unique; used as route key |
| `name` | string | Display name / real name |
| `email` | string | Unique |
| `email_verified_at` | timestamp | null = unverified |
| `password` | string | Hashed |
| `remember_token` | string | |
| `popname` | string | Optional "popular name" / handle |
| `about` | mediumtext | Profile bio |
| `location` | string | |
| `favouriteMovie` | string | |
| `favouriteMusic` | string | |
| `private` | boolean | Hides personal fields on profile; default true |
| `administrator` | boolean | Full admin access |
| `is_guest` | boolean | Read-only account; denied all write policies |
| `banned` | boolean | |
| `totalVisits` | int | Login count |
| `totalPosts` | int | Post count (denormalised) |
| `ipaddress` | string | Last known IP |
| `currentActivity` | string | Last page visited |
| `latestLogin` | timestamp | |
| `theme_id` | unsigned int | FK → themes; nullable |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | timestamp | Soft delete |

## Relationships

| Relation | Type | Notes |
|----------|------|-------|
| `posts` | hasMany Post | All posts authored by this user |
| `modifiedPosts` | hasMany Post | Posts where this user was the last editor |
| `comments` | hasMany Comment | Comments left on this user's profile |
| `givenComments` | hasMany Comment | Comments this user left on others' profiles |
| `sections` | hasMany Section | Sections this user moderates |
| `views` | hasMany View | Topic read-tracking records |
| `mentions` | hasMany Mention | `@username` mentions in posts |
| `chats` | hasMany Chat | Private conversations owned by this user |
| `unreadChats` | hasMany Chat | Filtered to `is_read = false` |
| `activity` | hasOne Activity | Latest page-visit record |
| `theme` | belongsTo Theme | Chosen theme; null = default |

## Scopes

- `scopeVerified` — users with a confirmed email address
- `scopeUnverified` — users still awaiting email verification

## Key methods

| Method | Description |
|--------|-------------|
| `isAdmin()` | Returns `true` when `administrator` is set |
| `isGuest()` | Returns `true` when `is_guest` is set |
| `newCommentCount()` | Count of unread profile comments |
| `markCommentsAsRead()` | Marks all profile comments read |
| `clearComments()` | Soft-deletes all profile comments |
| `unreadChatCount()` | Count of unread private chats |
| `notificationCount()` | Sum of unread chats + comments + mentions |
| `addMention(Post)` | Records a mention notification |
| `removeMentions(array)` | Clears mention records for given posts |
| `incrementTotalPosts()` | Increments the denormalised post counter |
| `getTrashedTopicsAttribute` | Soft-deleted topics in sections this user moderates |

## Model events

- **deleting** — cascades soft-delete to posts, comments, sections, views, activity, givenComments; nulls `update_user_id` on any posts this user edited
- **created** — fires `UserCreated` event (used for logging / notifications)

## Authorization

Policies are in `app/Policies/UserPolicy.php`. The `is_guest` flag is checked before any admin/owner shortcuts — a guest cannot edit their own settings even though they own their account.
