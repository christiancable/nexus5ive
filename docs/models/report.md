# Report

A moderation report raised against a piece of content (currently Posts). Polymorphic, so it could cover other model types in future. Reports have a status lifecycle and can accumulate moderation notes.

## Table: `reports`

| Column | Type | Notes |
|--------|------|-------|
| `id` | big int | Primary key |
| `reportable_type` | string | Polymorphic type (e.g. `App\Models\Post`) |
| `reportable_id` | unsigned big int | Polymorphic ID |
| `reporter_id` | unsigned big int | FK → users; nullable (anonymous reports) |
| `reason` | string | One of the `REASONS` constants |
| `details` | text | Free-text elaboration (nullable) |
| `reported_content_snapshot` | json | Snapshot of the reported content at time of report |
| `status` | string | One of the `STATUSES` constants; default `new`; indexed |
| `moderator_id` | unsigned big int | FK → users; assigned moderator (nullable) |
| `reviewed_at` | timestamp | When a moderator last acted (nullable) |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | timestamp | Soft delete |

## Constants

```php
Report::STATUSES  // ['new' => 'New', 'under_review' => 'Under Review', 'closed' => 'Closed']
Report::REASONS   // ['spam', 'harassment', 'hate_speech', 'illegal_content', 'other']
```

## Relationships

| Relation | Type | Notes |
|----------|------|-------|
| `reportable` | morphTo | The reported model (e.g. a Post) |
| `reporter` | belongsTo User | Who filed the report; nullable |
| `moderator` | belongsTo User | Assigned moderator; nullable |
| `moderationNotes` | hasMany ModerationNote | Staff notes added during review |

## Scopes

- `scopeOpen` — reports not yet closed
- `scopeClosed` — reports with `status = 'closed'`

## Accessors

| Accessor | Description |
|----------|-------------|
| `reportable_link` | URL to the reported content (Post only) |
| `status_badge_class` | Bootstrap badge class for the current status |
| `status_label` | Human-readable status from `STATUSES` |
| `reason_label` | Human-readable reason from `REASONS` |
| `snapshot_text` | Preview text from `reported_content_snapshot['text']` |
