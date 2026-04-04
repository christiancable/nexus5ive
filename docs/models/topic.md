# Topic

A discussion thread within a Section. Topics contain Posts. Topics can be pinned, made read-only, or marked secret.

## Table: `topics`

| Column | Type | Notes |
|--------|------|-------|
| `id` | unsigned int | Primary key |
| `title` | text | |
| `intro` | mediumtext | Optional description / preamble |
| `section_id` | unsigned int | FK → sections |
| `secret` | boolean | Hidden from non-moderators; default false |
| `readonly` | boolean | No new posts allowed; default false. All imported Nexus 2 topics are set to true. |
| `sticky` | boolean | Pinned to top in user-topic sections |
| `weight` | int | Sort order in moderator-curated sections; default 0 |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | timestamp | Soft delete |

## Relationships

| Relation | Type | Notes |
|----------|------|-------|
| `section` | belongsTo Section | |
| `posts` | hasMany Post | Ordered by `id` ascending (chronological) |
| `reversedPosts` | hasMany Post | Ordered by `id` descending (newest first) |
| `views` | hasMany View | Read-tracking records for this topic |
| `most_recent_post` | belongsTo Post | Injected via global scope (see below) |

## Global scope

A global scope `with_most_recent_post` is added in `boot()`. It augments every query with a correlated subquery that selects the most recent `post.id` for the topic, stored as `most_recent_post_id`. This enables efficient "last post" display without a separate query per topic.

## Key methods

| Method | Description |
|--------|-------------|
| `getMostRecentPostTimeAttribute` | The `time` of the most recent post, or `created_at` if no posts exist |

## Model events

- **deleting** — soft-deletes all related posts and views
- **created** — fires `TreeCacheBecameDirty`
- **updated** — fires `MostRecentPostForSectionBecameDirty` for the *original* section (handles moves), then `TreeCacheBecameDirty`
- **deleted** — fires `TreeCacheBecameDirty`
