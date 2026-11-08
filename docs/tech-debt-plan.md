# Tech Debt Plan — nexus5ive
**Created**: 2026-06-28  
**Branch**: `tech-debt/code-quality-review`  
**Focus**: Human-readable code and test confidence

---

## Sprint 1 — Unblock CI Confidence

### 1. Fix `phpunit.xml` to include `tests/intergration`
**Priority**: Critical  
**Effort**: S (30 min)  
**Status**: [x] Done

The `tests/intergration/` directory is not registered in `phpunit.xml`, so `ViewHelperTest` and `RestoreHelperTest` never run. CI is silently missing these tests.

**Files**
- `phpunit.xml`
- `tests/intergration/helpers/ViewHelperTest.php`
- `tests/intergration/helpers/RestoreHelperTest.php`

**Acceptance Criteria**
- [ ] `tests/intergration` added to `phpunit.xml`
- [ ] `sail artisan test --compact` runs and all tests pass (or newly-visible failures are fixed)
- [ ] Consider renaming to `tests/Integration` for consistency while here

---

### 2. Resolve Composer security advisories
**Priority**: Critical  
**Effort**: S (1–2 hrs)  
**Status**: [x] Done

Three CVEs reported by `composer audit`:
- **CVE-2026-55767** — `guzzlehttp/guzzle` — cookie domains with a dot match all hosts
- **CVE-2026-55568** — `guzzlehttp/guzzle` — silent HTTPS→cleartext proxy downgrade
- **CVE-2026-55766** — `guzzlehttp/psr7` — CRLF injection in HTTP start-line

Fix: `composer update guzzlehttp/guzzle guzzlehttp/psr7`

**Acceptance Criteria**
- [ ] `composer audit` reports 0 high/critical issues
- [ ] `sail artisan test --compact` passes

---

### 3. Fix `RestoreController::index` Larastan error
**Priority**: Critical  
**Effort**: S (1 hr)  
**Status**: [x] Done

`onlyTrashed()` is called on a `HasMany` relationship result at line 31. Larastan flags this as `method.notFound`. If the method doesn't exist at runtime this is a `BadMethodCallException`.

**File**: `app/Http/Controllers/Nexus/RestoreController.php:29–38`

**Acceptance Criteria**
- [ ] Verify `Section` uses `SoftDeletes` — if it does, add a type annotation; if not, rewrite the query
- [ ] Larastan passes with 0 errors (`sail npm run larastan`)
- [ ] `RestoreControllerTest` passes

---

## Sprint 2 — Readability and Safety

### 4. Add return types to all `ViewHelper` methods
**Priority**: Medium  
**Effort**: S (1 hr)  
**Status**: [x] Done

All 7 public static methods in `ViewHelper` lack return type declarations. `topicHasUnreadPosts` also has confusing logic — it returns `false` when the user has never read the topic, which contradicts the name (the method is not used in isolation from `getTopicStatus` but the semantics are surprising).

**File**: `app/Helpers/ViewHelper.php`

**Acceptance Criteria**
- [ ] All public methods have explicit PHP 8.3 return types
- [ ] `topicHasUnreadPosts` has a doc comment clarifying the "never read → false" behaviour, or the logic is corrected
- [ ] `sail npm run larastan` passes
- [ ] `tests/intergration/helpers/ViewHelperTest.php` passes

---

### 5. Extract shared `findViewRecord()` in `ViewHelper`
**Priority**: Medium  
**Effort**: S (45 min)  
**Status**: [x] Done

The query `View::where('topic_id', $topic->id)->where('user_id', $user->id)->first()` appears 5 times across `updateReadProgress`, `getReadProgress`, `getTopicStatus`, `unsubscribeFromTopic`, and `subscribeToTopic`.

**File**: `app/Helpers/ViewHelper.php`

**Acceptance Criteria**
- [ ] Private static `findViewRecord(User $user, Topic $topic): ?View` method added
- [ ] All 5 call sites use it
- [ ] No behaviour change — existing `ViewHelperTest` still passes

---

### 6. Clean up `SearchController::find`
**Priority**: Medium  
**Effort**: S (1 hr)  
**Status**: [x] Done

Three issues in one method:
1. Leftover debug comment `// dd($searchTerms);` on line 90
2. `$results` starts as `false` then becomes an Eloquent `Builder` — mixed types confuse readers and static analysis
3. Phrase-detection regex `^['|"](.*)['|"]$` treats `|` as a literal character in the character class, not as alternation — so `|text|` would also be detected as a phrase

**File**: `app/Http/Controllers/Nexus/SearchController.php`

**Acceptance Criteria**
- [ ] Debug comment removed
- [ ] Query-building extracted to a private method returning `Builder` so `$results` has a single clear type
- [ ] Regex corrected to `^['"](.*)['"]$`
- [ ] `SearchControllerTest` passes

---

### 7. Scope `User::all()` in `SectionController::show`
**Priority**: High  
**Effort**: S (30 min)  
**Status**: [x] Done

`User::all(['id', 'username'])` is called on every moderator page view, loading the entire users table to populate a dropdown.

**File**: `app/Http/Controllers/Nexus/SectionController.php:84`

**Acceptance Criteria**
- [ ] Changed to `User::verified()->orderBy('username')->get(['id', 'username'])`
- [ ] Moderator section page still renders the full user list for the dropdown
- [ ] `sail artisan test --compact` passes

---

## Backlog

### 8. Migrate `action()` calls to named `route()` calls
**Priority**: Medium  
**Effort**: L (half day)  
**Status**: [x] Done

20+ uses of `action('App\Http\...\ControllerName@method', [...])` across 6 controllers. Named routes are refactor-safe and are the Laravel convention. Several methods in the same controllers already use `route()`.

**Files**: `SectionController`, `RestoreController`, `SearchController`, `UserController`, `ChatController`, `ActivityController`

**Acceptance Criteria**
- [ ] All `action()` calls replaced with `route()` using named routes
- [ ] `sail artisan route:list` shows all routes have names
- [ ] Full test suite passes

---

### 9. Fix `User::boot()` deleting hook
**Priority**: Medium  
**Effort**: M (2–3 hrs)  
**Status**: [x] Done

Two issues:
1. Uses `get_class($user->$child) === 'Illuminate\Database\Eloquent\Collection'` — fragile string class comparison; use `instanceof` instead
2. Deletes related records one-by-one in a PHP loop (N+1 queries per delete)

**File**: `app/Models/User.php:99–134`

**Acceptance Criteria**
- [ ] `instanceof` used instead of `get_class()` string comparison
- [ ] Related record deletion uses batch queries where model events don't need to fire
- [ ] Existing user deletion tests cover the cascade behaviour

---

### 10. Type the `Chat` Livewire component's public properties
**Priority**: Low  
**Effort**: S (30 min)  
**Status**: [x] Done

8 untyped public properties (`$users`, `$messages`, `$chats`, `$user`, `$newMessage`, `$selectedUser`, `$selectedChat`, `$pollingInterval`, `$newChatUser`). Livewire 4 serialises public properties; untyped props can cause unexpected hydration behaviour.

**File**: `app/Livewire/Chat.php`

**Acceptance Criteria**
- [ ] All public properties have PHP type declarations
- [ ] Livewire chat feature works end-to-end (browser test or manual check)

---

### 11. Resolve `User::getTrashedTopicsAttribute` TODO
**Priority**: Low  
**Effort**: M (1–2 hrs)  
**Status**: [x] Done

A 5+ year old comment: `// @todo: why does the hasManyThrough not work here?`. The accessor manually loads sections then topics. Either wire up `hasManyThrough(Topic::class, Section::class)` correctly, or document _why_ the accessor approach is intentional and remove the TODO.

**File**: `app/Models/User.php:204–218`

**Acceptance Criteria**
- [ ] TODO resolved: either replaced with a working relationship or replaced with an explanatory comment
- [ ] Restore feature still works (archived topics visible)

---

### 12. Bump minor/patch dependencies
**Priority**: Low  
**Effort**: S (30 min)  
**Status**: [ ] Todo

Non-security updates available:
- `laravel/framework` 13.15.0 → 13.17.0
- `livewire/livewire` 4.3.1 → 4.3.3
- `pestphp/pest` 4.7.2 → 4.7.4
- `laravel/pint` 1.29.1 → 1.29.3

**Acceptance Criteria**
- [ ] `composer update` for the above packages
- [ ] `sail artisan test --compact` passes

---

## Progress Summary

| # | Item | Priority | Effort | Status |
|---|---|---|---|---|
| 1 | Fix `phpunit.xml` for `tests/intergration` | Critical | S | ✅ Done |
| 2 | Resolve Composer security advisories | Critical | S | ✅ Done |
| 3 | Fix `RestoreController` Larastan error | Critical | S | ✅ Done |
| 4 | Add return types to `ViewHelper` | Medium | S | ✅ Done |
| 5 | Extract `findViewRecord()` in `ViewHelper` | Medium | S | ✅ Done |
| 6 | Clean up `SearchController::find` | Medium | S | ✅ Done |
| 7 | Scope `User::all()` in `SectionController` | High | S | ✅ Done |
| 8 | Migrate `action()` to `route()` | Medium | L | ✅ Done |
| 9 | Fix `User::boot()` deleting hook | Medium | M | ✅ Done |
| 10 | Type `Chat` Livewire properties | Low | S | ✅ Done |
| 11 | Resolve `User` `hasManyThrough` TODO | Low | M | ✅ Done |
| 12 | Bump minor/patch dependencies | Low | S | Backlog |
