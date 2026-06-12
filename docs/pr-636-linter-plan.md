# PR 636 — Laravel Shift Linter: Remediation Plan

## Status

| # | Task | Status |
|---|------|--------|
| 1 | Move `resources/lang` → `lang/` | ✅ Done |
| 2 | Fix `Auth::user()` → `$request->user()` in `ReportController` | ✅ Done |
| 3 | Create `StoreReport` + `UpdateReport` Form Requests | ✅ Done |
| 4 | Add return type hints to `ReportController` | ✅ Done |
| 5 | Audit and update config files against Laravel 13 defaults | ✅ Done |
| 6 | Broad type hints pass (167 methods across all controllers/models) | ✅ Done |
| 7 | Non-resource controller refactor | ❌ Skipped |

---

## Completed Changes

### 1. `resources/lang` → `lang/`

- Moved `resources/lang/en/nexus.php` to `lang/en/nexus.php`
- Ran `artisan lang:publish` to add standard framework lang files:
  `lang/en/auth.php`, `lang/en/pagination.php`, `lang/en/passwords.php`, `lang/en/validation.php`

### 2 & 3. `ReportController` refactor

- Replaced `Auth::user()` / `Auth::check()` / `Auth::id()` with `$request->user()`
- Extracted inline validation from `store()` and `update()` into Form Requests:
  - `app/Http/Requests/Nexus/StoreReport.php` — `authorize()` returns `true` (any auth user can report)
  - `app/Http/Requests/Nexus/UpdateReport.php` — `authorize()` checks `$this->user()->isAdmin()`
- Added `tests/Feature/ReportControllerTest.php` with 8 tests covering store, update, authorization, and validation

### 4. Return type hints on `ReportController`

- Added `View` and `RedirectResponse` return types to all implemented methods
- Stub methods (`create`, `edit`, `destroy`) typed as `void`
- Fixed unreachable `default` arm in second `match` expression (PHPStan `match.alwaysTrue`)

### 5. Config file updates

| File | Change |
|------|--------|
| `config/logging.php` | Added `(string)` cast to `env('LOG_STACK', ...)` |
| `config/database.php` | Added PHP 8.5+ conditional for `MYSQL_ATTR_SSL_CA`; added `transaction_mode` to SQLite config |
| `config/session.php` | Added `(int)` cast to `SESSION_LIFETIME` env |
| `config/mail.php` | Renamed `encryption` → `scheme`; added `(string)` cast to `parse_url()` env arg |
| `config/queue.php` | Added `deferred`, `background`, `failover` queue connections |
| `config/cache.php` | Added `failover` cache store |
| `config/filesystems.php` | Updated local disk root to `app/private`, added `serve` and `report` options; fixed S3 URL default |

---

### 6. Broad type hints pass

39 files (controllers, models, Form Requests, Livewire components) received PHP 8 return type declarations.

**Key decisions:**
- Livewire action methods that `return redirect()` were left untyped — Livewire 4 returns its own `Livewire\Features\SupportRedirects\Redirector` class (not `Illuminate\Http\RedirectResponse`), so declaring `RedirectResponse` causes a runtime `TypeError` that manifests as a 403 abort in the Livewire request handler. Affected methods: `PostCompose::save()`, `SearchMenu::performSearch()`.
- Eloquent relationship methods typed with specific relation classes (`BelongsTo`, `HasMany`, `HasOne`, `MorphTo`, `MorphMany`).
- `Userlist::fetchUsers()` annotated with `@return Collection<int, User>` PHPDoc alongside the PHP type hint so PHPStan can resolve the generic element type.
- Pre-existing PHPStan errors left untouched: `RestoreController` (`HasMany::onlyTrashed()` — Larastan limitation), `Nexus2/Importer` (`usort` unresolvable type).

## Skipped: Non-resource controller actions

The flagged non-resource actions (`SectionController::latest`, `TopicController::updateSubscription`, etc.) are intentional domain operations for a BBS. Splitting them into single-action controllers would be a large refactor with no functional benefit.

---

## Auth controllers (inline validation — skipped)

`ConfirmablePasswordController`, `NewPasswordController`, `PasswordResetLinkController`, `RegisteredUserController` are Breeze-generated boilerplate. Converting their inline validation to Form Requests adds noise without benefit; left as-is.
