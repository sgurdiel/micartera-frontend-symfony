# AGENTS.md

## Purpose

This repository contains the Symfony frontend for MiCartera.

## Stack

- PHP 8.5
- Symfony 8
- Twig templates
- PHPUnit for application tests
- Node-based asset sources under `assets/` with built files in `public/build/`
- Runtime tooling managed through `devbox` and `mise`

## Project Layout

- `src/`: application code
- `templates/`: Twig views
- `config/`: Symfony packages, routes, and bundle registration
- `tests/application/`: test suite covered by `phpunit.xml`
- `assets/`: frontend source assets
- `public/`: web root and compiled/static browser assets

## Working Rules

- Prefer small Symfony-native changes over new abstractions.
- Keep controller, form, and template changes aligned; avoid fixing only one layer.
- Do not edit files under `var/`, `vendor/`, or `node_modules/`.
- Treat `public/build/` as generated output unless the task explicitly requires updating built assets.
- Preserve existing translation usage and Twig block structure.

## Validation

- If `php`, `composer`, or `node` are not on `PATH`, check the project runtime via `devbox` and `mise` before assuming the tool is unavailable.
- Run PHPUnit with `vendor/bin/phpunit -c phpunit.xml` when PHP dependencies are available.
- If changing Symfony configuration, also verify the container can boot with `bin/console` commands when available.
- For frontend changes, review both the Twig template and the matching asset source under `assets/`.

## Notes

- Bundle registration lives in `config/bundles.php`.
- Auto-scripts are defined in `composer.json`.
- Asset-related config is split between `package.json`, `webpack.config.js`, and `config/packages/`.
- `devbox.json` defines the PHP runtime and extensions used for local work.
- `mise.toml` defines the Node runtime from values loaded through `versions.env` and optional local env overrides.
