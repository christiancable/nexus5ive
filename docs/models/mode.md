# Mode

A named configuration preset that sets the active theme and welcome message for the BBS. At most one Mode should be `active` at a time. When `override` is true, the Mode's theme is forced on all users regardless of their personal theme setting.

## Table: `modes`

| Column | Type | Notes |
|--------|------|-------|
| `id` | big int | Primary key |
| `name` | text | Label for this mode (e.g. "Default", "Archive") |
| `welcome` | longtext | Welcome message shown on the front page (nullable) |
| `theme_id` | unsigned int | FK → themes; nullable |
| `active` | boolean | Whether this mode is currently in effect; default false |
| `override` | boolean | Forces `theme_id` on all users when true; default false |
| `created_at` / `updated_at` | timestamps | |

## Fillable

`name`, `welcome`, `theme_id`, `active`, `override`

## Relationships

| Relation | Type | Notes |
|----------|------|-------|
| `theme` | belongsTo Theme | The theme applied when this mode is active |

## Scopes

- `scopeActive` — filters to modes where `active = true`

## How it works

`AddBBSModeToView` middleware runs on every request. It loads the active Mode (with its Theme eager-loaded) and shares it as `$mode` to all views. The result is cached for one hour under the key `bbs_mode`.

When a mode is saved or the active mode is switched (via the admin Settings page), the cache is explicitly cleared with `Cache::forget('bbs_mode')`.

The `ModeController` exists as a resource controller stub but all meaningful mode management is handled by the `Settings` Livewire component (`app/Livewire/Settings.php`), which:

- Lists all modes and allows switching between them
- Lets admins edit the welcome message, theme, and override flag for any mode
- Sets the chosen mode as active (deactivating all others first)

## Initial setup

`nexus:install` creates a default Mode via `Mode::factory()->make(...)` if none exists, associating it with the first available Theme.

## Authorization

`ModePolicy::update()` restricts edits to administrators. The `Settings` Livewire component checks `$request->user()->cannot('update', Mode::class)` before saving.
