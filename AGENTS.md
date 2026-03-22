# Repository Guidelines

## Project Structure & Module Organization
This repository is a mostly flat PHP application (file manager) served from the project root.
- Core entry points: `index.php`, `login.php`, `api.php`, `webdav.php`.
- Shared bootstrap/layout: `_init.php`, `_header.php`, `_footer.php`.
- Reusable PHP helpers: `lib/`.
- Frontend assets: `js/` (source + bundle), `style.css`, `icon/`.
- DB helper area: `db/`.
- Runtime/output folders: `tmp/` (local cache/temp), `vendor/` (Composer), `node_modules/` (pnpm).

## Build, Test, and Development Commands
- Install PHP deps: `php composer.phar install` (or `composer install` if available).
- Install JS deps: `pnpm install`.
- Build editor bundle: `pnpm build` (Rollup builds `js/edit_code.bundle.js` from `js/edit_code.js`).
- Run local server: `php -S 127.0.0.1:9753`.

## Coding Style & Naming Conventions
- PHP style follows PSR-12 with 4-space indentation (`.php-cs-fixer.dist.php`).
- Keep procedural page scripts in root with snake_case file names (for example: edit_text.php, delete_multi.php).
- Keep shared logic in `lib/` and avoid duplicating helpers across page scripts.

