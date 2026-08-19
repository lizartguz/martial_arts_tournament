---
name: csp-security
description: Content-Security-Policy rules for this project (script-src/style-src, nonces). Use when adding or editing a `<script>` tag in any `.blade.php` view, adding a third-party/CDN script or stylesheet (analytics, chat widgets, payment SDKs, fonts, maps, tracking pixels), editing `app/Http/Middleware/SecurityHeaders.php`, working with `onclick=`/`onerror=`/`onchange=` native HTML attributes, Livewire `@once <script>` blocks, `@vite()` or `@livewireScripts` directives, or any task mentioning CSP, Content-Security-Policy, nonce, script-src, style-src, XSS mitigation, or Content-Security-Policy violations in the browser console.
---

# CSP Security

Full background and rationale: `docs/politica_seguridad_csp.md`. Read it
once before doing non-trivial CSP work; this skill is the quick-reference
workflow.

## Current policy (source of truth: `app/Http/Middleware/SecurityHeaders.php`)

```
script-src 'self' 'unsafe-eval' 'unsafe-inline' 'nonce-{per-request}' https://www.googletagmanager.com
script-src-attr 'unsafe-inline'
style-src 'self' 'unsafe-inline' https://fonts.bunny.net
```

The nonce is served by `App\Support\CspNonce::value()`, a per-request
singleton (bound in `AppServiceProvider`). It is **not** shared via
`View::share()` on purpose — `Livewire::test()` bypasses the middleware, and
a shared variable would be undefined there.

## Workflow

1. Identify what kind of script/style is being touched:
   - **Inline `<script>...</script>` (no `src=`)** — must carry
     `nonce="{{ \App\Support\CspNonce::value() }}"`. Without it, the browser
     silently blocks it; there is no server-side error.
   - **JS file loaded via `@vite(...)` or `asset(...)`** — same-origin,
     already covered by `'self'`. No nonce, no allowlist entry needed.
   - **`<script src="https://third-party.example.com/...">`** — add that
     origin to `script-src` in `SecurityHeaders.php`, or it is blocked.
   - **`<link rel="stylesheet" href="https://...">` from a new host** — same,
     but add to `style-src`.
   - **Alpine directives (`x-data`, `@click`, `x-show`, ...)** — not native
     browser attributes; Alpine reads and evaluates them itself via
     `'unsafe-eval'`. No CSP work needed, ever.
   - **Native `onclick=`/`onerror=`/etc.** — currently permitted via
     `script-src-attr 'unsafe-inline'`. Existing ones can stay; for new code
     prefer Alpine's `@click` instead of adding more native handlers.

2. If a `@once <script>` block is added inside a Livewire component view
   (same pattern as `resources/views/components/tw-select.blade.php`), it
   still needs the nonce — `@once` only controls how many times it prints,
   not CSP eligibility.

3. **Do not trust "it works locally" alone for inline scripts.** Laravel
   Debugbar (`APP_ENV=local`) makes the CSP response go out *without* a
   nonce on purpose, because Debugbar's own bundle injects dynamic content
   `setCspNonce()` doesn't cover, and a present nonce would otherwise disable
   `'unsafe-inline'` for that untouched content too. That means a
   non-nonced inline script can pass silently in local dev and get blocked
   for real in staging/production, where Debugbar never runs. Verify with
   Debugbar off, or via the browser test suite (next step).

## Verification

Browser console is the only reliable signal — a CSP violation produces no
Laravel log, no exception, a normal 200 response, and a feature that just
silently does nothing.

```bash
npm run test:browser        # headless, tests/Playwright/security/csp.spec.js
npm run test:browser:ui     # interactive, for debugging a specific failure
```

`tests/Playwright/fixtures.js` exposes an `adminPage` fixture (logs in as the
seeded `sarteaga@root.dev` / `super_manager` user) for any new test that
needs an authenticated session.

Manual check: open the affected page, DevTools console, look for `Refused
to...` or `Content Security Policy directive`. `msg.location()` in a
Playwright console listener gives the exact source line if the message alone
isn't enough (as used to find the Vite/Debugbar-injected scripts that no
grep over `.blade.php` files could catch, since neither is literal Blade
template text).

## Reporting

When finishing work that touches scripts, styles, or `SecurityHeaders.php`,
mention:

- Whether any new inline script/style was added, and whether it carries the
  nonce.
- Whether any new external domain was added to the allowlist, and why.
- Whether `npm run test:browser` was run and passed.
- If Debugbar was involved in testing, note whether verification happened
  with it on, off, or both.
