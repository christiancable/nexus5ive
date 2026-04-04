# ModerationNote

A staff note attached to a Report, used to record actions taken or observations made during moderation review. Multiple notes can be added to a single report over its lifetime.

## Table: `moderation_notes`

| Column | Type | Notes |
|--------|------|-------|
| `id` | big int | Primary key |
| `report_id` | big int | FK → reports |
| `user_id` | unsigned int | FK → users; the moderator who wrote the note (nullable) |
| `user_name` | string | Snapshot of the moderator's name at time of writing (nullable) |
| `note` | text | The note body |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | timestamp | Soft delete |

## Relationships

| Relation | Type | Notes |
|----------|------|-------|
| `report` | belongsTo Report | |
| `user` | belongsTo User | Uses `withDefault()` — safe to access even if the moderator account has been deleted |

## Notes

`user_name` is snapshotted at write time so that username changes or account deletions don't remove the attribution from historical notes. The `withDefault()` on the `user` relation ensures `$note->user` always returns a model instance rather than null.
