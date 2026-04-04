# Nexus 2 Archive Mode — Implementation Plan

## Status

**Core archive mode: ✅ COMPLETE**
**Nexus 2 decompression support: ✅ COMPLETE**
**Nexus 2 full import command: ✅ COMPLETE**

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

## How read-only enforcement works

There are two independent mechanisms. Understanding both is important.

### `is_guest` flag (per-user)

`is_guest` is a boolean on the `User` model. When true, all write policies return
`false` for that user — regardless of any other flags including `administrator`.

Checks happen in: `PostPolicy::create()`, `CommentPolicy::create()`,
`UserPolicy::update()`, `ChatPolicy::create()`, and `CommentController::destroyAll()`.

**All users created by `nexus2:import` get `is_guest = true` automatically** —
legacy accounts, placeholder accounts, and the SysOp account. This means the archive
is read-only from the moment import completes, with no `.env` changes needed.

The fresh admin account (created outside the import) is unaffected and retains full
write access. To grant a specific imported user write access:

```bash
php artisan tinker
> $user = User::where('username', 'theirusername')->firstOrFail();
> $user->is_guest = false;
> $user->save();
```

### `NEXUS_ARCHIVE_MODE` (global lockout)

`NEXUS_ARCHIVE_MODE=true` in `.env` registers a `Gate::before()` hook that returns
`false` for `create`, `update`, `delete`, and `restore` abilities for **every** user,
including the admin. It is a hard maintenance lockout.

```
NEXUS_ARCHIVE_MODE=true   # nobody can write, including admins
NEXUS_ARCHIVE_MODE=false  # normal operation, is_guest still applies per-user
```

### How they interact

| Scenario | `is_guest` | `NEXUS_ARCHIVE_MODE` |
|----------|-----------|---------------------|
| Imported legacy user blocked | ✓ | ✓ |
| Shared Guest account blocked | ✓ | ✓ |
| Fresh admin account blocked | ✗ | ✓ |
| Per-user granular control | ✓ | ✗ |

**In practice:** `is_guest` handles day-to-day enforcement. `NEXUS_ARCHIVE_MODE`
is only needed if you want to prevent the admin from writing too — e.g. during
a content freeze or before handing the archive to someone else.

---

## What users can do

| Action | Guest (shared) | Legacy user (imported) | Admin |
|--------|---------------|----------------------|-------|
| Browse sections | ✓ | ✓ | ✓ |
| Read topics & posts | ✓ | ✓ | ✓ |
| View user profiles | ✓ | ✓ | ✓ |
| Search | ✓ | ✓ | ✓ |
| Post / reply | ✗ | ✗ | ✓ |
| Comment on profiles | ✗ | ✗ | ✓ |
| Change settings / theme | ✗ | ✗ | ✓ |
| Access admin panel | ✗ | ✗ | ✓ |

Legacy users can be individually unlocked (set `is_guest = false`) if they want to
reclaim their account and participate.

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

**`app/Nexus2/ArticleParser.php`** — updated constructor:
`__construct(string $filePath, ?string $content = null)` — uses `$content`
directly if provided, otherwise reads from disk.

**`app/Nexus2/Importer.php`** — `importArticle()` detects the `k` flag, reads
the raw compressed file, calls `Decompressor::decompress()`, and passes the
result to `ArticleParser`.

---

### 10. All imported users are read-only by default ✅

`nexus2:import` sets `is_guest = true` on all created users — legacy accounts,
placeholder accounts (for post authors not found in the UDB), and the SysOp
system account. This means the archive is read-only immediately after import
without any `.env` changes.

---

### 11. Undated posts ✅

Posts with no timestamp in the source data store `null` in the `time` column
rather than a fake epoch date. Views display "Date unknown" for these posts.
The leap function skips topics where no dated posts exist.

---

### 12. Default mode created on import ✅

`nexus2:import` creates a default `Mode` record (if none exists) pointing to the
Nexus 2 theme with `override = true`. The `Settings` Livewire component also
creates a default Mode if none exists when the admin first visits the settings page.

---

### 13. Default theme: Nexus 2 for all users

In the archive install, force the Nexus 2 theme globally using the existing
**Modes** system — create an active Mode with `override = true` pointing to the
Nexus 2 theme. The import handles this automatically (step 12).

---

### 14. Disable registration

Set in `.env`:

```
NEXUS_ALLOW_REGISTRATIONS=false
```

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

- **`tests/Browser/NextTest.php`** — leap tests covering:
  - Topic with only undated posts does not crash leap
  - Topic with mixed dated/undated posts uses most recent dated post time

---

## Deployment steps

1. Clone the repo and configure `.env`:
   ```
   APP_NAME="Nexus 2 Archive"
   NEXUS_ALLOW_REGISTRATIONS=false
   ```
2. Run migrations (`php artisan migrate`)
3. Build frontend assets (`npm run build`)
4. Register the Nexus 2 theme: `php artisan nexus:theme add --name="Nexus 2" --path="resources/sass/nexus2.scss"`
5. Run `php artisan nexus2:import` — imports all content, creates default Mode, sets all users to `is_guest = true`
6. Run `php artisan nexus:create-guest Guest` to create the shared Guest account
7. Set the Guest account password: `php artisan nexus:set-password Guest`
8. Share the Guest credentials with former Nexus users

`NEXUS_ARCHIVE_MODE=true` is optional — only needed if you want to lock out the
admin account too (e.g. before handing the archive to someone else).

---

## Access model

**Both shared Guest account and individual legacy accounts.**

- The shared Guest account is for casual browsing
- All imported legacy accounts are read-only by default (`is_guest = true`)
- Former users who want to reclaim write access can contact the admin, who sets
  `is_guest = false` for their account and assigns a password via
  `php artisan nexus:set-password {username}`

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
