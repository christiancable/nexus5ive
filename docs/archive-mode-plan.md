# Nexus 2 Archive Mode — Implementation Plan

## Status

**Core archive mode: ✅ COMPLETE**
**Nexus 2 decompression support: ✅ COMPLETE**
**Nexus 2 full import command: 🔲 Pending** (see separate plan)

---

## Overview

Deploy a separate Nexus5ive installation as a **private read-only archive** of the
Nexus 2 BBS (1993–2001). Access is restricted to people who are given the credentials
for a shared **Guest account** — not open to the public internet.

### Privacy rationale

The content in this archive was written by university students in the early-to-mid
1990s. They wrote it for a private BBS with no expectation that it would ever be
publicly accessible on the web, decades later. Making it fully public would be
inappropriate. Access should be limited to the original community — people who were
there — via shared credentials.

---

## Approach: `is_guest` account level + shared credentials ✅

Authentication stays in place — the site is not public. Access is controlled by
giving people either the Guest account credentials or the password to their own
imported legacy account.

A new **`is_guest` boolean flag** is added to the users table, mirroring the
existing `administrator` flag. A Guest user account is created with this flag set.
Policies check `is_guest` to deny all write operations, so the flag works for the
shared Guest account and can equally be applied to any other account that should
be read-only.

- No self-registration — you either have your original legacy account or you use Guest
- The Nexus 2 theme is enforced globally

This requires minimal code changes and fits naturally into the existing
authorization architecture.

---

## What the Guest account can do

| Action | Guest | Legacy user (imported) |
|--------|-------|----------------------|
| Browse sections | ✓ | ✓ |
| Read topics & posts | ✓ | ✓ |
| View user profiles | ✓ | ✓ |
| Search | ✓ | ✓ |
| Post / reply | ✗ | ✗ (all topics readonly) |
| Comment on profiles | ✗ | ✗ |
| Change settings / theme | ✗ | ✓ (own settings only) |
| Access admin panel | ✗ | ✗ |

All imported topics are already `readonly = true`, so even legacy account holders
cannot post. The Guest account just needs to be additionally prevented from
commenting on user profiles and changing settings.

---

## Implementation steps

### 1. Migration — add `is_guest` to users table ✅

`database/migrations/2026_02_22_000000_add_is_guest_to_users_table.php`

```php
$table->boolean('is_guest')->default(false)->after('administrator');
```

---

### 2. User model ✅

`app/Models/User.php`

- Added `'is_guest' => 'bool'` to `casts()`
- Added `isGuest(): bool` convenience method

---

### 3. New artisan command — `nexus:create-guest` ✅

`app/Console/Commands/NexusCreateGuest.php`

```
php artisan nexus:create-guest {username}
```

Creates a user with `is_guest = true`. Email is derived as `{username}@nexus.local`.
Fails if username already exists.

---

### 4. Policy changes — `is_guest` blocks all writes ✅

All policies check `is_guest` first (before admin/moderator shortcuts):

- **`PostPolicy::create()`** — returns `false` for guests
- **`CommentPolicy::create()`** — new method, returns `false` for guests
- **`UserPolicy::update()`** — returns `false` for guests
- **`ChatPolicy`** — new policy class (`app/Policies/ChatPolicy.php`), `create()` returns `false` for guests

The `StoreComment` FormRequest `authorize()` method was updated to call
`$this->user()->can('create', Comment::class)` rather than returning `true`.

---

### 5. Hide write UI for guest accounts ✅

Views updated with `@can` / `@cannot` Blade directives (single source of truth
with policies):

- `resources/views/nexus/topics/_addpost.blade.php` — `@can('create', [App\Models\Post::class, $topic])`
  (array form required to target `PostPolicy`, not `TopicPolicy`)
- `resources/views/nexus/users/show.blade.php` — comment form, settings form, clear-comments button
- `resources/views/livewire/profile-menu.blade.php` — chat link

`Chat::sendMessage()` in `app/Livewire/Chat.php` also calls
`$this->authorize('create', \App\Models\Chat::class)` for belt-and-braces backend enforcement.

---

### 6. `NEXUS_ARCHIVE_MODE` gate override ✅

`config/nexus.php` — `'archive_mode' => env('NEXUS_ARCHIVE_MODE', false)`

`app/Providers/AppServiceProvider.php` — `Gate::before()` hook:

```php
Gate::before(function (User $user, string $ability) {
    if (config('nexus.archive_mode') && in_array($ability, [
        'create', 'update', 'delete', 'restore',
    ])) {
        return false; // deny all write actions for everyone, including admins
    }
});
```

This makes the entire install read-only when `NEXUS_ARCHIVE_MODE=true`. Admins
toggle the flag to `false` to perform maintenance tasks.

---

### 7. `destroyAll` (clear profile comments) blocked for guests ✅

`app/Http/Controllers/Nexus/CommentController.php` — `destroyAll()` checks
`$request->user()->cannot('update', $request->user())` and aborts with 403.

---

### 8. Profile privacy extension ✅

`resources/views/nexus/users/_read.blade.php` — the `private` flag now hides
email, location, favouriteMovie, and favouriteMusic (previously only email).
Username, about text, and statistics remain visible.

The Importer sets `$user->private = true` when the UDB `Hide` field equals
`'All (invisible)'`.

---

### 9. Nexus 2 decompression support ✅

Nexus 2 article files can be stored compressed (flagged with `k` in the MNU).
The compression format is documented in `COMPRESS.C` (the original 1993–1994 C source).

**`app/Nexus2/Decompressor.php`** — new class implementing the full decompression
algorithm:

| Byte range | Meaning |
|-----------|---------|
| `0x00–0x7F` | Literal byte, output as-is |
| `0x80–0xFD` | Digram: look up two-char pair from 126-entry DATA table |
| `0xFE` | Extended: next byte is a literal high-byte character |
| `0xFF` | Run-length: `[char][count]` — repeat char count times |

Pre-processing normalises DOS line endings (`\r\n` → `\n`) and strips the DOS
EOF marker (`\x1A`) before byte-level decoding, matching what the original C
compressor assumed about its input.

The compressor avoids run-length counts of 26 (`\x1A`) and 13 (`\x0D`) by
splitting them into count-1 + extra literal; the decompressor handles this
naturally without special logic.

**`app/Nexus2/ArticleParser.php`** — updated constructor:
`__construct(string $filePath, ?string $content = null)` — uses `$content`
directly if provided, otherwise reads from disk.

**`app/Nexus2/Importer.php`** — `importArticle()` detects the `k` flag, reads
the raw compressed file, calls `Decompressor::decompress()`, and passes the
result to `ArticleParser`.

---

### 10. Default theme: Nexus 2 for all users

In the archive install, force the Nexus 2 theme globally using the existing
**Modes** system — create an active Mode with `override = true` pointing to the
Nexus 2 theme. No code change needed — this is a database/configuration step
at install time.

---

### 11. Disable registration

Set in `.env`:

```
NEXUS_ALLOW_REGISTRATIONS=false
```

This already hides the "Join" button. Note: search engine indexing is not a
concern — the site requires login for all content, so crawlers cannot access
anything beyond the login page.

---

## Tests ✅

- **`tests/Feature/GuestAccountTest.php`** — 29 tests covering:
  - `isGuest()` helper
  - Policy `can()` checks for all four policies
  - HTTP 403 enforcement for comment/post/settings/chat
  - `NEXUS_ARCHIVE_MODE` `Gate::before` — all users blocked including admins
  - Profile privacy view (private flag hides personal fields)
  - Administrator + `is_guest` edge cases (admin flag overridden by guest check)
  - `destroyAll` forbidden for guest users

- **`tests/Unit/Nexus2/DecompressorTest.php`** — 24 tests covering:
  - Literal bytes, CRLF normalisation, DOS EOF stripping
  - Digram substitution (spot checks + full data provider)
  - Extended escape (`0xFE`) including truncated edge case
  - Run-length encoding (`0xFF`) including truncated edge case
  - Compressor 26/13 count split round-trip
  - Mixed sequence validated against known BLAKE7 file header
  - Integration test against real compressed BLAKE7 file (skipped if fixture absent)

---

## Deployment steps

1. Clone the repo and configure `.env`:
   ```
   APP_NAME="Nexus 2 Archive"
   NEXUS_ARCHIVE_MODE=true
   NEXUS_ALLOW_REGISTRATIONS=false
   ```
2. Run migrations (`php artisan migrate`)
3. Run `php artisan nexus:install` to create the admin user and root section
4. Run `php artisan nexus2:import` to import all legacy content
5. Run `php artisan nexus:create-guest Guest` to create the shared Guest account
6. Set up an active Mode with `override = true` pointing to the Nexus 2 theme
7. Share the Guest credentials with former Nexus users (e.g. via a private message
   to known members, or a page on an existing community site)

---

## Access model

**Option C (chosen)**: Both shared Guest account and individual legacy accounts.

Start with a shared Guest account for casual browsing. Former users who want to
reclaim their identity can contact the admin, who sets a password for their imported
account via `php artisan tinker` or a future `nexus:set-password` command.

---

## Open questions

1. **Individual account password reset** — legacy accounts have placeholder emails
   (`@legacy.nexus2`). Users who want to reclaim their account need the admin to
   manually set a password or update their real email first.

2. **Personal info on profile pages** — `name`, `location`, `favouriteMovie`,
   `favouriteMusic`, `about` are hidden when the imported user's `private` flag is
   set. For others the fields are visible to logged-in users only (auth required
   for all pages).

3. **Who gets told about the archive?** Consider sending a message to the Nexus
   mailing list / Facebook group / wherever former members still gather, with the
   URL and Guest credentials.

4. **Retention policy** — How long should the archive be kept? Individuals may
   later ask for their content to be removed (right to erasure). Having a clear
   policy and an admin contact address is advisable.
