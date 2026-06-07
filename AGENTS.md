# Repository Guidelines

## Project Structure & Module Organization

This repository is a mostly flat PHP 7.4 application (file manager) served from the project root.
- Shared bootstrap/layout: `_init.php`, `_function.php`, `_header.php`, `_footer.php`.
- Frontend assets: `js/` (source + bundle), `style.css`, `icon/`.
- DB helper area: `db/`.
- Runtime/output folders: `vendor/` (Composer), `node_modules/` (pnpm).

## Coding Style & Naming Conventions

- PHP style follows PSR-12 with 4-space indentation.
- Keep procedural page scripts in root with snake_case file names (for example: file_edit_text.php, multi_delete.php).

## Testing

- Only check syntax.
