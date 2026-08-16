# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Filament Money Field is a Laravel/Filament package providing localized money fields (form input, table column, infolist entry) powered by [Money PHP](https://www.moneyphp.org/). It supports Filament 4 and Filament 5. The heavy formatting logic lives in the dependency `pelmered/larapara` (LaraPara).

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

Three dependency sets live side by side, each in its own vendor directory. `f5`
is the default `vendor/` and needs no driver; the others are built by
`scripts/lane.php`, which generates `composer-<lane>.json` and
`phpunit-<lane>.xml` from `composer.json` and `phpunit.xml` so no lane can drift
from the real constraints. All generated files are gitignored.

| Lane | Filament | Laravel | PHP | Serves on |
| --- | --- | --- | --- | --- |
| `f4` — lowest supported | 4 | 11 (testbench `^9.0`) | 8.3 | `:8004` |
| `l12` — latest Filament, previous Laravel | 5 | 12 (testbench `^10.0`) | 8.4 | `:8006` |
| `f5` — latest stable (default `vendor/`) | 5 | 13 (testbench `^11.0`) | 8.5 | `:8000` |

```bash
# Install or refresh an environment
composer env:f5     composer env:f4     composer env:l12

# Run the suite against one
composer test:f5    composer test:f4    composer test:l12

# Run the Browser suite (needs "npm ci" + "npx playwright install chromium")
composer test:browser:f5   composer test:browser:f4   composer test:browser:l12

# Serve the workbench app — all three can run at once
composer serve:f5   composer serve:f4   composer serve:l12
```

The `browser` job in `.github/workflows/tests.yml` mirrors these three lanes.
Add a lane by adding an entry to `LANES` in `scripts/lane.php` and the matching
`env:`/`test:`/`serve:` scripts.

The `f4` lane targets PHP 8.3 rather than the package floor of 8.2 because
`pest-plugin-browser` requires `^8.3`. Each lane pins `config.platform.php` to
its target, so dependencies resolve as CI resolves them even when the installed
binary is a different version; the scripts print the runtime PHP and flag the
difference when it applies.

`serve:*` boots a Filament panel at `/admin` with no login — `Workbench\App\Http\Middleware\AutoLogin`
signs in the seeded demo user. The panel's brand name reports the running Filament,
Laravel and Livewire versions, so you can always see which environment you are on.
`workbench/` is shared source, so one demo resource renders under both majors.

Laravel 11 does not run on PHP 8.5, so the F4 scripts dispatch to a PHP 8.4 binary
(`php84` or `php8.4` on `PATH`; override with `PHP_F4=/path/to/php`).

**Testbench assumes the package vendor directory is named `vendor`.** Two places
would otherwise silently run the F5 code from the F4 environment, and both are
handled in `scripts/f4.php`:
- The served skeleton bootstraps from `<package>/vendor/autoload.php`, so
  `env:f4`/`serve:f4` rewrite `vendor-f4/orchestra/testbench-core/laravel/bootstrap/autoload.php`
  and abort loudly if that rewrite ever stops matching.
- `testbench package:test` resolves the runner as `vendor/pestphp/pest/bin/pest`,
  so `test:f4` invokes `vendor-f4/bin/pest` directly instead.

## Architecture

### Core Components (src/)

- **`Forms/Components/MoneyInput`** — Extends Filament's `TextInput`. Handles formatting state for display (`formatStateUsing`) and parsing back to `Money` objects (`dehydrateStateUsing`). Supports currency switcher action, input mask, symbol placement, min/max validation.
- **`Tables/Columns/MoneyColumn`** — Extends `TextColumn`. Formats `Money|int|string` state for display. Supports `short()` format (e.g. "$1.23M").
- **`Infolists/Components/MoneyEntry`** — Extends `TextEntry`. Same pattern as MoneyColumn.
- **`Concerns/HasMoneyAttributes`** — Shared trait used by all three components. Provides `currency()`, `locale()`, `decimals()`, `currencyColumn()`, and related getters. Currency resolution falls back: field → record's currency column → default config.
- **`Synthesizers/`** — Livewire property synthesizers for `Money` and `Currency` objects (hydrate/dehydrate for Livewire state).
- **`Casts/MoneyCast`** — Eloquent cast for Money objects.
- **`Forms/Rules/MinValueRule`, `MaxValueRule`** — Custom validation rules that parse localized input before comparing.

### Filament 4 and 5 Compatibility

The package supports Filament 4 and 5 from one codebase. The two majors share an identical PHP API (`Filament\Schemas\Schema`, `Filament\Actions\Action`, etc.); the difference is Livewire (v3 on Filament 4, v4 on Filament 5), handled entirely by composer constraints. CI pins each Filament major explicitly via the `filament` matrix axis in `.github/workflows/tests.yml`.

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
- `filament/support` — Filament framework (supports v4.0+ and v5.0+)
- `ext-intl` — Required for locale-aware number formatting
