# Repository Guidelines

## Project Structure & Module Organization
This repository is a mostly flat PHP 7.4+ application (file manager) served from the project root.
- Shared bootstrap/layout: `_init.php`, `_header.php`, `_footer.php`.
- Reusable PHP helpers: `lib/`.
- Frontend assets: `js/` (source + bundle), `style.css`, `icon/`.
- DB helper area: `db/`.
- Runtime/output folders: `tmp/` (local cache/temp), `vendor/` (Composer), `node_modules/` (pnpm).

## Coding Style & Naming Conventions
- PHP style follows PSR-12 with 4-space indentation (`.php-cs-fixer.dist.php`).
- Keep procedural page scripts in root with snake_case file names (for example: edit_text.php, delete_multi.php).
- Keep shared logic in `lib/` and avoid duplicating helpers across page scripts.
