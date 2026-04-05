# Comment

A short message left on a User's profile page by another user. Comments are publicly visible to anyone who views the profile; unread comments generate a notification badge for the profile owner.

## Table: `comments`

| Column | Type | Notes |
|--------|------|-------|
| `id` | unsigned int | Primary key |
| `text` | text | Comment body |
| `read` | boolean | Whether the profile owner has seen it; default false |
| `user_id` | unsigned int | FK → users; the profile being commented on |
| `author_id` | unsigned int | FK → users; who wrote the comment |
| `created_at` / `updated_at` | timestamps | |

## Relationships

| Relation | Type | Notes |
|----------|------|-------|
| `user` | belongsTo User | The profile owner (recipient) |
| `author` | belongsTo User | The comment author |

## Authorization

- `CommentPolicy::create()` — only non-guest authenticated users may post comments.
- `CommentPolicy::delete()` — moderators and the profile owner may delete individual comments.
- `destroyAll` (clear all comments from a profile) — requires `UserPolicy::update()` on the profile owner; blocked for guest accounts.

## Notifications

`User::newCommentCount()` counts comments where `read = false` on a user's profile. `User::markCommentsAsRead()` sets all to `read = true`. These drive the notification badge in the UI.
