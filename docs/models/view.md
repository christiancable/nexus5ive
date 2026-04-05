# View

Tracks which topics a user has visited and when. Used to determine unread post counts and to drive the subscription system.

## Table: `views`

| Column | Type | Notes |
|--------|------|-------|
| `id` | unsigned int | Primary key |
| `user_id` | unsigned int | FK → users |
| `topic_id` | unsigned int | FK → topics |
| `latest_view_date` | timestamp | When the user last read this topic |
| `unsubscribed` | boolean | User has opted out of notifications for this topic; default false |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | timestamp | Soft delete |

## Relationships

| Relation | Type | Notes |
|----------|------|-------|
| `user` | belongsTo User | |
| `topic` | belongsTo Topic | |

## Scopes

- `scopeSubscribed` — filters to records where `unsubscribed = false`

## Usage

A `View` record is created or updated whenever a user reads a topic. Comparing `latest_view_date` against post `time` values reveals which posts the user hasn't seen yet. When a topic is soft-deleted, its associated view records are also soft-deleted.
