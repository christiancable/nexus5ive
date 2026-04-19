# Pest 4 Browser Testing Migration

Migration from Laravel Dusk (Selenium/ChromeDriver) to Pest 4 browser testing (Playwright).
**Migration complete** — all 42 browser tests passing.

## Why

Playwright is fundamentally more reliable than Selenium/WebDriver. It auto-waits for elements
before acting, has no session-expiry crashes, and handles Livewire's async behaviour cleanly
without explicit `pause()` or `waitFor*` calls.

---

## Running browser tests

```bash
# All browser tests
sail php vendor/bin/pest --browser chrome tests/Browser/

# Single file
sail php vendor/bin/pest --browser chrome tests/Browser/LoginTest.php
```

Playwright browsers were installed via:

```bash
sail npm install playwright@latest
sail npx playwright install chromium
```

---

## Authentication

The Pest browser plugin's HTTP server runs **in-process** — it dispatches browser requests
through `app()->make(HttpKernel::class)->handle()` in the same PHP process as the tests.
Because of this, `actingAs()` from `Pest\Laravel` works directly:

```php
use function Pest\Laravel\actingAs;

test('user can see their profile', function () {
    $user = User::factory()->create();
    actingAs($user);
    visit('/users/' . $user->username)->assertSee($user->name);
});
```

`actingAs($user)` calls `$this->app['auth']->guard()->setUser($user)`, which sets the user
on the auth guard singleton. Since the kernel and guard singletons are shared across all
requests in the same process, auth persists for every browser request within the test.

No test-only login route is needed.

---

## Bootstrap (`tests/Pest.php`)

```php
<?php

use Illuminate\Foundation\Testing\DatabaseTruncation;
use Tests\BrowserTestCase;

uses(BrowserTestCase::class, DatabaseTruncation::class)->in('Browser');

pest()->browser()->timeout(20000);
```

`DatabaseTruncation` runs `migrate:fresh` once per suite and truncates tables between tests.
The `themes` table is excluded via `BrowserTestCase` because it is seeded by a migration
rather than a dedicated seeder.

`tests/BrowserTestCase.php`:

```php
<?php

namespace Tests;

abstract class BrowserTestCase extends TestCase
{
    protected array $exceptTables = ['themes'];
}
```

---

## Selector mapping

Pest browser's `@foo` shorthand maps to `[data-testid=foo], [data-test=foo]` — **not**
`[dusk=foo]`. All views have `data-test` attributes added alongside the existing `dusk`
attributes. The `dusk` attributes remain but are no longer used by tests.

| Dusk | Pest / Playwright |
|---|---|
| `$this->browse(fn($browser) {...})` | `$page = visit('/')` — no wrapper |
| `$browser->loginAs($user)` | `actingAs($user)` before `visit()` |
| `$browser->visit('/path')` | `visit('/path')` |
| `$browser->assertSee($text)` | `->assertSee($text)` |
| `$browser->assertDontSee($text)` | `->assertDontSee($text)` |
| `$browser->assertSeeIn('@sel', $text)` | `->assertSeeIn('@sel', $text)` |
| `$browser->assertPresent('@sel')` | `->assertPresent('@sel')` |
| `$browser->assertMissing('@sel')` | `->assertMissing('@sel')` |
| `$browser->assertVisible('@sel')` | `->assertVisible('@sel')` |
| `$browser->assertSelected('@sel', $val)` | `->assertSelected('@sel', $val)` |
| `$browser->assertSelectHasOption('@sel', $val)` | `->assertPresent('option[value="'.$val.'"]')` |
| `$browser->assertPathIs('/path')` | `->assertPathIs('/path')` |
| `$browser->type('field', 'value')` | `->type('field', 'value')` |
| `$browser->press('@sel')` | `->press('@sel')` |
| `$browser->click('#sel')` | `->click('#sel')` |
| `$browser->clickLink('text')` | `->click('text')` |
| `$browser->select('#sel', $val)` | `->select('#sel', $val)` |
| `$browser->clear('@sel')` | `->clear('@sel')` |
| `$browser->waitFor('@sel')` | Drop — Playwright auto-waits |
| `$browser->waitForText('text')` | `->assertSee('text')` |
| `$browser->waitUntilMissing('@sel')` | `->assertMissing('@sel')` |
| `$browser->waitForLocation('/path')` | `->assertPathIs('/path')` |
| `$browser->scrollIntoView('@sel')` | Drop — Playwright scrolls automatically |
| `$browser->script('...')` | `->script('...')` |
| `$browser->pause(200)` | Drop |
| `$browser->with('@sel', fn($b) {...})` | `->assertSeeIn('@sel', ...)` |
| `$this->browse(fn($a, $b) {...})` | Single `visit()` — seed messages via PHP helpers |

---

## Known gotchas

**Playwright strict mode**: Selectors must match exactly one element. CSS selectors like
`button.btn-danger:first-of-type` can match multiple elements (one per container) and will
throw. Scope the selector or use `nth=0`:

```php
// Bad — matches one button per table row = multiple elements
->press('button.btn-danger:first-of-type')

// Good — scoped to the first table row
->click('.user-comments tr:first-child button.btn-danger')
```

**`multipart/form-data` and method spoofing**: The in-process server only parses
`application/x-www-form-urlencoded` bodies. Forms with `enctype="multipart/form-data"` will
not have their `_method` field parsed, so Laravel sees a `POST` instead of `PATCH`/`PUT`.
Remove `enctype="multipart/form-data"` from forms that have no file inputs.

**`navigate()` vs `visit()`**: `navigate()` navigates the existing browser page (like
clicking a link); `visit()` is a top-level function that opens a URL in the browser context.
Use `$page->navigate('/path')` to follow a link from an existing page variable.
