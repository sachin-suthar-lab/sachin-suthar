# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Stock WordPress install served locally via WAMP at `e:/wamp/www/projects/sachin`. Site URL is typically `http://localhost/projects/sachin/`. No version control, no custom themes/plugins, no build tooling — just the WordPress core checkout plus default bundled themes (`twentytwentythree`, `twentytwentyfour`, `twentytwentyfive`) and the `akismet` / `hello.php` plugins.

There is no project-specific code yet. Any work here will be either WordPress admin configuration or adding a new theme/plugin under `wp-content/`.

## Local environment

- DB: MySQL on `localhost`, database `sachin-db`, user `root`, empty password (see [wp-config.php](wp-config.php)). Manage via WAMP's bundled phpMyAdmin.
- PHP / Apache: served by WAMP — start the WAMP tray app to bring the site up.
- `WP_DEBUG` is `false`. Flip it to `true` in [wp-config.php](wp-config.php) when debugging PHP errors locally.

## Working in wp-content

When adding custom code, put it under [wp-content/themes/](wp-content/themes/) (new theme dir) or [wp-content/plugins/](wp-content/plugins/) (new plugin dir or file). Do **not** modify files under `wp-admin/`, `wp-includes/`, or the bundled `twenty*` themes — those are upstream WordPress and get overwritten on core/theme updates. If a bundled theme needs changes, make a child theme instead.

Uploaded media lives in [wp-content/uploads/](wp-content/uploads/) and should not be hand-edited.
