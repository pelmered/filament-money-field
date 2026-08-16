# AGENTS.md

This file provides guidance to Codex (Codex.ai/code) when working with code in this repository.

## Project Overview

Filament Money Field is a Laravel/Filament package providing localized money fields (form input, table column, infolist entry) powered by [Money PHP](https://www.moneyphp.org/). It supports both Filament 3 and Filament 4. The heavy formatting logic lives in the dependency `pelmered/larapara` (LaraPara).

## Common Commands

```bash
# Run all tests
composer test
# or directly:
vendor/bin/pest

# Run a single test file
vendor/bin/pest tests/Components/FormInputTest.php

# Run a single test by name
vendor/bin/pest --filter="accepts form input money in numeric format"

# Lint (Pint + PHPStan)
composer lint

# Auto-fix (Pint + Rector + PHPStan)
composer fix

# PHPStan only (level 8)
composer phpstan

# Pint only
composer pint

# Type coverage
composer types

# Test coverage
composer coverage
```

### Test Environments

Three dependency sets live side by side, each in its own vendor directory.
`latest` is the default `vendor/` and needs no driver; the others are built by
`scripts/test-env.php`, which generates `composer-<name>.json` and
`phpunit-<name>.xml` from `composer.json` and `phpunit.xml` so an environment
cannot drift from the real constraints. All generated files are gitignored.

| Environment | Filament | Laravel | PHP | Serves on |
| --- | --- | --- | --- | --- |
| `lowest` — lowest supported | 4 | 11 (testbench `^9.0`) | 8.3 | `:8004` |
| `middle` — latest Filament, previous Laravel | 5 | 12 (testbench `^10.0`) | 8.4 | `:8006` |
| `latest` — default `vendor/` | 5 | 13 (testbench `^11.0`) | 8.5 | `:8000` |

```bash
# Install or refresh an environment
composer env:lowest    composer env:middle    composer env:latest

# Run the suite against one
composer test:lowest   composer test:middle   composer test:latest

# Run the Browser suite (needs "npm ci" + "npx playwright install chromium")
composer test:browser:lowest   composer test:browser:middle   composer test:browser:latest

# Serve the workbench app — all three can run at once
composer serve:lowest  composer serve:middle  composer serve:latest
```

The `browser` job in `.github/workflows/tests.yml` uses the same three names.
Add an environment by adding an entry to `TEST_ENVIRONMENTS` in
`scripts/test-env.php`, the matching `env:`/`test:`/`serve:` scripts, and its
vendor directory to `exclude` in `pint.json` — Pint only skips `vendor` by
default, so an unlisted one gets linted like source.

`lowest` targets PHP 8.3 rather than the package floor of 8.2 because
`pest-plugin-browser` requires `^8.3`. Each environment pins
`config.platform.php` to its target, so dependencies resolve as CI resolves them
even when the installed binary is a different version; the scripts print the
runtime PHP and flag the difference when it applies (override with
`PHP_LOWEST` / `PHP_MIDDLE`).

`serve:*` boots a Filament panel at `/admin` with no login — `Workbench\App\Http\Middleware\AutoLogin`
signs in the seeded demo user. The panel's brand name reports the running Filament,
Laravel and Livewire versions, so you can always see which environment you are on.
`workbench/` is shared source, so one demo resource renders under both majors.

Laravel 11 does not run on PHP 8.5, so `lowest` never uses the ambient PHP when it
is 8.5; it searches upward from its target to its ceiling and stops at 8.4.

**Testbench assumes the package vendor directory is named `vendor`.** Two places
would otherwise silently run the default environment instead, and both are
handled in `scripts/test-env.php`:
- The served skeleton bootstraps from `<package>/vendor/autoload.php`, so
  `env:*`/`serve:*` rewrite `vendor-<name>/orchestra/testbench-core/laravel/bootstrap/autoload.php`
  and abort loudly if that rewrite ever stops matching.
- `testbench package:test` resolves the runner as `vendor/pestphp/pest/bin/pest`,
  so `test:*` invokes `vendor-<name>/bin/pest` directly instead.

## Architecture

### Core Components (src/)

- **`Forms/Components/MoneyInput`** — Extends Filament's `TextInput`. Handles formatting state for display (`formatStateUsing`) and parsing back to `Money` objects (`dehydrateStateUsing`). Supports currency switcher action, input mask, symbol placement, min/max validation.
- **`Tables/Columns/MoneyColumn`** — Extends `TextColumn`. Formats `Money|int|string` state for display. Supports `short()` format (e.g. "$1.23M").
- **`Infolists/Components/MoneyEntry`** — Extends `TextEntry`. Same pattern as MoneyColumn.
- **`Concerns/HasMoneyAttributes`** — Shared trait used by all three components. Provides `currency()`, `locale()`, `decimals()`, `currencyColumn()`, and related getters. Currency resolution falls back: field → record's currency column → default config.
- **`Synthesizers/`** — Livewire property synthesizers for `Money` and `Currency` objects (hydrate/dehydrate for Livewire state).
- **`Casts/MoneyCast`** — Eloquent cast for Money objects.
- **`Forms/Rules/MinValueRule`, `MaxValueRule`** — Custom validation rules that parse localized input before comparing.
- **`Helper`** — Contains `isFilament3()` which checks the installed Filament version at runtime via `Composer\InstalledVersions`.

### Filament 3 vs 4 Compatibility

The package supports both Filament 3 and 4. Key differences are handled via `Helper::isFilament3()`:
- Filament 4 uses `Filament\Schemas\Schema` and `Filament\Actions\Action`
- Filament 3 uses `Filament\Forms\ComponentContainer` and `Filament\Forms\Components\Actions\Action`
- Test helpers in `tests/Pest.php` branch on this (see `createTestComponent`, `resolveFilament3Components`, `getComponent`)

### Test Structure (tests/)

- **`Components/`** — Integration tests for MoneyInput, MoneyEntry, MoneyColumn (uses Livewire test components)
- **`Unit/`** — Unit tests for service provider, synthesizers, casts
- **`Support/`** — Test Livewire components (`FormTestComponent`, `InfolistTestComponent`, `TableTestComponent`) and model/factory
- **`Pest.php`** — Global test helpers: `createTestComponent()`, `createFormTestComponent()`, `getComponent()`, `getComponentState()`, `validationTester()`, `replaceNonBreakingSpaces()`

### Configuration

Config file: `config/filament-money-field.php`. Key settings (all overridable via `.env`):
- `MONEY_DEFAULT_LOCALE`, `MONEY_DEFAULT_CURRENCY`
- `MONEY_USE_INPUT_MASK`, `MONEY_DECIMAL_DIGITS`
- `MONEY_UNIT_PLACEMENT` (before/after/hidden)
- `MONEY_CURRENCY_SWITCHER_ENABLED`

### Code Style

- **Pint**: Laravel preset with aligned binary operators (`pint.json`)
- **PHPStan**: Level 8 with Larastan and php-static-analysis extensions
- **Rector**: PHP 8.2 target with code quality, coding style, dead code, privatization, type declaration, early return sets. Skips `ClosureToArrowFunctionRector`.

### Dependencies

- `moneyphp/money` — Core Money value objects
- `pelmered/larapara` — Currency repository, MoneyFormatter (formatting, parsing, rules), Currency value objects, migration macros
- `filament/support` — Filament framework (supports v3.2.39+ and v4.0+)
- `ext-intl` — Required for locale-aware number formatting
