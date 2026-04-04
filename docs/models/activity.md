# Activity

Records the most recent page visited by a user. One record per user (upserted on each page load). Used to show "currently online" / last-seen information.

## Table: `activities`

| Column | Type | Notes |
|--------|------|-------|
| `id` | unsigned int | Primary key |
| `user_id` | unsigned int | Unique FK → users; one record per user |
| `text` | text | Human-readable description of the page (nullable) |
| `route` | text | The route name or URL (nullable) |
| `time` | timestamp | When the activity was recorded |
| `created_at` / `updated_at` | timestamps | |

## Relationships

| Relation | Type | Notes |
|----------|------|-------|
| `user` | belongsTo User | |

## Notes

The `user_id` column has a unique constraint, enforcing the one-record-per-user design. When a user is deleted, their activity record is soft-deleted as part of the cascading delete in `User::boot()`.
