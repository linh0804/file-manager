# Plan: Replace Config-Based Login Fail Tracking with File-Based Tracking

## Context

Login fail tracking currently stores count and timestamp in `.env.php` via `config()->set()`. Every fail rewrites the entire config JSON — overkill. Replace with a lightweight file `tmp_app_login_fail` stored at `__DIR__` (app root), using the file's mtime for expiry checking instead of a stored timestamp. Define the path as a constant `LOGIN_LOCK_PATH` in `_init.php`.

## Exact changes

### 1. `_init.php` — Add `LOGIN_LOCK_PATH` constant, remove `LOGIN_LOCK_KEY`

Replace line 39:

```php
// old
define('LOGIN_LOCK_KEY', 'login_fail');

// new
define('LOGIN_LOCK_PATH', __DIR__ . '/tmp_app_login_fail');
```

`LOGIN_MAX` (5) and `LOGIN_WAIT` (1800) stay unchanged.

### 2. `_function.php` — Replace 4 function bodies (lines 40-68)

```php
function get_login_fail()
{
    if (!is_file(LOGIN_LOCK_PATH)) {
        return 0;
    }
    // auto-reset nếu đã hết thời gian chờ (dùng file mtime)
    if (filemtime(LOGIN_LOCK_PATH) + LOGIN_WAIT < time()) {
        @unlink(LOGIN_LOCK_PATH);
        return 0;
    }
    return (int) file_get_contents(LOGIN_LOCK_PATH);
}

function increase_login_fail()
{
    $count = get_login_fail() + 1;
    file_put_contents(LOGIN_LOCK_PATH, (string) $count, LOCK_EX);
}

function reset_fail_login()
{
    if (is_file(LOGIN_LOCK_PATH)) {
        unlink(LOGIN_LOCK_PATH);
    }
}

function can_login()
{
    return get_login_fail() < LOGIN_MAX;
}
```

No `@` suppression or fallbacks — the file is stored locally in the app directory where we control permissions. If it fails, it fails loud (correct behaviour per user request).

### 3. `_footer.php` — No change needed

Still calls `get_login_fail()` — function signature unchanged.

### 4. `.env.php` — No change needed

Orphaned `login_fail` / `login_fail_time` keys are silently ignored.

## How it works

| Operation | Effect |
|---|---|
| Wrong password | `increase_login_fail()` → reads count (auto-resets if expired), writes count+1 to file, mtime updates automatically |
| Lockout (≥5 fails) | `can_login()` returns false → login form shows lock message |
| Successful login | `reset_fail_login()` → unlinks the file |
| Auto-reset after 30 min | `get_login_fail()` sees `mtime + 1800 < time()` → unlinks file, returns 0 |

## Verification

1. Wrong password 5× → lockout appears, `ls -la /home/www/web/ngatngay.net/-/tmp_app_login_fail` shows content "5"
2. Change `LOGIN_WAIT` to 10 temporarily → after 10s, login works again, file is gone
3. Successful login → file deleted
4. `grep -r LOGIN_LOCK_KEY .` → no references remain
