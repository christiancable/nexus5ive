# Section

A forum category. Sections are hierarchical — each section can have a parent section and any number of child sections. The root section has no parent. Topics live inside sections.

## Table: `sections`

| Column | Type | Notes |
|--------|------|-------|
| `id` | unsigned int | Primary key |
| `title` | text | |
| `intro` | mediumtext | Optional description shown to users |
| `user_id` | unsigned int | FK → users; the section's moderator |
| `parent_id` | unsigned int | FK → sections; null for the root |
| `weight` | int | Sort order within parent; default 0 |
| `allow_user_topics` | boolean | When true, members can create new topics |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | timestamp | Soft delete |

## Relationships

| Relation | Type | Notes |
|----------|------|-------|
| `moderator` | belongsTo User | via `user_id` |
| `parent` | belongsTo Section | The containing section |
| `sections` | hasMany Section | Direct child sections, ordered by `weight` |
| `topics` | hasMany Topic | Topics within this section (ordering depends on `allow_user_topics`) |
| `trashedTopics` | hasMany Topic | Soft-deleted topics |

## Topic ordering

- **`allow_user_topics = false`** (moderator-curated): topics ordered by `weight` only; `sticky` has no effect.
- **`allow_user_topics = true`** (member-driven): sticky topics first, then by most recent post time descending (falls back to topic `created_at` when no posts exist).

## Key methods

| Method | Description |
|--------|-------------|
| `allChildSections()` | Returns a flat `Collection` of all descendant sections (recursive) |
| `getMostRecentPostAttribute` | The most recent `Post` within this section; cached forever, invalidated by `MostRecentPostForSectionBecameDirty` |
| `forgetMostRecentPostAttribute($id)` | Clears the cache for a given section |
| `getIsHomeAttribute` | `true` when `parent_id` is null (root section) |
| `slug()` | URL-safe slug derived from `title` |

## Caching

`most_recent_post` is stored in the application cache under the key `mostRecentPost{id}`. The cache is populated on first access and cleared whenever a post is created or deleted within any topic belonging to this section (via the `MostRecentPostForSectionBecameDirty` event).

The section tree (used for navigation) is rebuilt on every create/update/delete via `TreeHelper::rebuild()`.

## Model events

- **deleting** — cascades soft-delete to child sections and topics (each recursively triggers their own delete events)
- **created / updated / deleted** — calls `TreeHelper::rebuild()` to refresh the navigation cache
