# Nexus 2 Import Guide

This guide covers importing legacy Nexus 2 BBS data into a fresh Nexus5ive installation. This path does **not** use `nexus:install` — the import command creates all necessary users, sections, topics, and posts directly.

## Prerequisites

- A working Nexus5ive installation with migrations run (`php artisan migrate`)
- The Nexus 2 data directory available (default: `untracked/ucl_info/BBS`)
- The expected directory structure:
  ```
  BBS/
  ├── USR/          (user directories, each containing NEXUS.UDB)
  ├── SECTIONS/     (menu files and article data)
  └── ONSTUFF/
      └── NEXUS.INI (main menu pointer)
  ```

---

## Step 1: Build the frontend assets

The Nexus 2 theme must be compiled before it can be used:

```bash
npm run build
```

---

## Step 2: Register the Nexus 2 theme

Themes are registered using their **source file path**. The `@vite()` Blade directive resolves this to the correct compiled output automatically, so the stored path never goes stale after a rebuild:

```bash
php artisan nexus:theme add --name="Nexus 2" --path="resources/sass/nexus2.scss"
```

---

## Step 3: Run the import

```bash
php artisan nexus2:import
```

This imports users, sections, topics, posts, and comments in a single database transaction. It is safe to re-run — already-imported records are tracked in the `nexus2_imports` table and skipped.

### Options

| Option | Description |
|--------|-------------|
| `--dry-run` | Walk all import logic and report counts without writing to the database |
| `--path=` | Path to the BBS data directory (default: `untracked/ucl_info/BBS`) |
| `--section=` | Import legacy menus as children of an existing section ID |
| `--merge-existing-users` | Match legacy nicks to existing accounts rather than creating `_legacy` duplicates |
| `--priv=` | Only import menu items with a read privilege at or below this level (default: 100) |

### Dry run first

```bash
php artisan nexus2:import --dry-run
```

Reports what would be created without touching the database. Useful for verifying the data path and checking counts before committing.

---

## Imported users are read-only by default

All users created during import (legacy accounts, placeholder accounts, and the SysOp account) are set to `is_guest = true`. This makes the entire archive read-only without requiring `NEXUS_ARCHIVE_MODE`.

To grant a specific user write access (e.g. someone who wants to reclaim their legacy account):

```bash
php artisan tinker
> $user = User::where('username', 'theirusername')->firstOrFail();
> $user->is_guest = false;
> $user->save();
```

`NEXUS_ARCHIVE_MODE` remains available as a hard lockout for everyone including the admin — useful during maintenance.

---

## Step 4: Create the guest account

```bash
php artisan nexus:create-guest Guest
```

Creates a shared read-only account (`is_guest = true`) for people who don't have their own legacy account. The email is set to `guest@nexus.local` and the password must be set separately (see below).

---

## Step 5: Set the guest account password

```bash
php artisan nexus:setpassword Guest
```

Follow the prompts to set a memorable shared password.

---

## Step 6: Configure the default mode

The admin **Settings** page (`/admin/theme`) manages modes. If no mode exists yet, visiting this page will automatically create a default one.

To set the Nexus 2 theme as the global default:

1. Log in as an administrator and go to **Admin → Theme**
2. Select the **Default** mode (or whichever mode is active)
3. Set **Theme** to **Nexus 2**
4. Tick **Override** — this forces the Nexus 2 theme on all users regardless of their personal setting
5. Click **Save**
6. Click **Set as active mode**

With `override = true`, every user (including the Guest account and all imported legacy users) will see the Nexus 2 theme.

---

## Step 7: Enable archive mode (optional)

If this installation is intended as a read-only archive, set in `.env`:

```
NEXUS_ARCHIVE_MODE=true
NEXUS_ALLOW_REGISTRATIONS=false
```

`NEXUS_ARCHIVE_MODE=true` blocks all write operations for everyone via a `Gate::before` hook. Toggle it to `false` temporarily when you need to make administrative changes.

See [archive-mode-plan.md](archive-mode-plan.md) for full details.

---

## Re-running the import

The import is idempotent. Each imported record is tracked by a `type` + `legacy_key` pair in the `nexus2_imports` table. Re-running will skip everything already imported and only process new records.

To start fresh, truncate `nexus2_imports` and the destination tables, then re-run.

---

## Post-import checks

```bash
# Counts
php artisan tinker
> User::count()
> Section::count()
> Topic::count()
> Post::count()

# Imported legacy users specifically
> User::where('email', 'like', '%@legacy.nexus2')->count()
```
