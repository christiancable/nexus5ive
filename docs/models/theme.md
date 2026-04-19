# Theme

A CSS stylesheet that can be selected by users to customise the look of the BBS. Themes can reference a local CSS file or an external URL.

## Table: `themes`

| Column | Type | Notes |
|--------|------|-------|
| `id` | unsigned int | Primary key (unique) |
| `name` | string | Display name; unique |
| `path` | string | Path to CSS file, or a full `http(s)://` URL for external themes |
| `created_at` / `updated_at` | timestamps | |

## Relationships

| Relation | Type | Notes |
|----------|------|-------|
| `users` | hasMany User | Users who have selected this theme |

## Accessors

| Accessor | Description |
|----------|-------------|
| `UCName` | `name` in title-case (`ucwords`) |
| `external` | `true` if `path` starts with `http` |

## Management

Themes are managed via Artisan:

```bash
php artisan nexus:theme add --name=Excelsior --path='/css/excelsior.css'
php artisan nexus:theme remove --name=minty
```

## Overriding globally

A `Mode` record with `override = true` forces a specific theme on all users regardless of their personal setting. Used in the archive deployment to enforce the Nexus 2 theme.
