# Post

A single message within a Topic. Posts are the primary content of the BBS.

## Table: `posts`

| Column | Type | Notes |
|--------|------|-------|
| `id` | unsigned int | Primary key |
| `title` | text | Optional subject line |
| `text` | longtext | The post body |
| `time` | timestamp | When the post was written (may differ from `created_at` for imported posts) |
| `popname` | text | Author's "popular name" at time of posting (snapshot, may differ from `user.popname`) |
| `html` | boolean | Whether `text` contains HTML; default false |
| `user_id` | unsigned int | FK → users; the original author |
| `topic_id` | unsigned int | FK → topics |
| `update_user_id` | unsigned int | FK → users; last editor (nullable) |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | timestamp | Soft delete |

## Relationships

| Relation | Type | Notes |
|----------|------|-------|
| `topic` | belongsTo Topic | |
| `author` | belongsTo User | via `user_id` |
| `editor` | belongsTo User | via `update_user_id`; the last person to edit the post |
| `reports` | morphMany Report | Moderation reports against this post |

## Model events

- **created** — fires `MostRecentPostForSectionBecameDirty` for the post's section
- **deleting** — fires `MostRecentPostForSectionBecameDirty` if this is the most recent post in its section (so the cache is refreshed to point at the new most-recent post)

## Notes

- `time` is set explicitly on imported Nexus 2 posts to preserve the original timestamp; `created_at` reflects when the record was inserted into the database.
- `popname` is snapshotted at write time so that a user's handle change doesn't retroactively alter old posts.
- When a user is deleted, their `update_user_id` references on other posts are nulled before the user record is soft-deleted.
