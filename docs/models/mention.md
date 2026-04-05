# Mention

Records an `@username` mention inside a Post, creating a notification for the mentioned user.

## Table: `mentions`

| Column | Type | Notes |
|--------|------|-------|
| `id` | unsigned int | Primary key |
| `post_id` | unsigned int | FK → posts; the post containing the mention |
| `user_id` | unsigned int | FK → users; the mentioned user |
| `read` | boolean | Whether the user has acknowledged it; default false |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | timestamp | Soft delete |

## Relationships

| Relation | Type | Notes |
|----------|------|-------|
| `post` | belongsTo Post | |
| `user` | belongsTo User | The mentioned user |

## Usage

Created by `User::addMention(Post $post)` when a post body contains `@username`. Cleared in bulk by `User::clearMentions()` or selectively by `User::removeMentions(array $posts)`. Unread mention counts contribute to the notification badge via `User::notificationCount()`.
