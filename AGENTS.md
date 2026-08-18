# AGENTS.md

Guidance for coding agents working in this repository. This is the single source
of truth: `CLAUDE.md` imports this file, so edit only this one.

## Project Overview

Filament Money Field is a Laravel/Filament package providing localized money fields (form input, table column, infolist entry) powered by [Money PHP](https://www.moneyphp.org/). It supports Filament 4 and Filament 5. The heavy formatting logic lives in the dependency `pelmered/larapara` (LaraPara).

## Common Commands

```bash
# Run the suite (Components + Unit; the Browser suite is excluded)
composer test

# Run the Browser suite (needs "npm ci" + "npx playwright install chromium")
composer test:browser

# Everything, Browser suite included
composer test:all

# Run a single test file, or a single test by name
vendor/bin/pest tests/Components/FormInputTest.php
vendor/bin/pest --filter="accepts form input money in numeric format"

# Lint (Pint + PHPStan), and lint + test together
composer lint
composer check

# PHPStan only (level 8), Pint only, Rector only
composer phpstan
composer pint
vendor/bin/rector

# Type coverage, test coverage
composer types
composer coverage
```

`composer fix` currently aborts on its first step — it passes `--fix` to Pint,
which has no such flag — so run `composer pint`, `vendor/bin/rector` and
`composer phpstan` instead.

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
| `latest` — default `vendor/` | 5 | 13 (testbench `^11.0`) | ambient (8.5 in CI) | `:8000` |

```bash
# Install or refresh an environment
composer env:lowest    composer env:middle    composer env:latest

# Run the suite against one
composer test:lowest   composer test:middle   composer test:latest

# Run the Browser suite against one
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
`pest-plugin-browser` requires `^8.3`. The two generated environments pin
`config.platform.php` to their target, so dependencies resolve as CI resolves
them even when the installed binary is a different version; the scripts print the
runtime PHP and flag the difference when it applies (override the binary with
`PHP_LOWEST` / `PHP_MIDDLE`). `latest` has no generated config and simply uses
the ambient PHP and `composer.json` as-is.

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
- **`Casts/MoneyCast`, `Casts/CurrencyCast`** — Eloquent casts. Both are thin subclasses of the LaraPara casts, kept so the documented `Pelmered\FilamentMoneyField\Casts\*` names keep working after the split.
- **`Exceptions/MissingMoneyCast`** — Thrown when a saved attribute is not cast to `Money`; the message names both casts to add.
- **`Forms/Rules/MinValueRule`, `MaxValueRule`** — Custom validation rules that parse localized input before comparing.

Formatting and parsing itself is `Pelmered\LaraPara\MoneyFormatter\MoneyFormatter`, imported from the dependency — there is no formatter class in this package.

### Filament 4 and 5 Compatibility

The package supports Filament 4 and 5 from one codebase. The two majors share an identical PHP API (`Filament\Schemas\Schema`, `Filament\Actions\Action`, etc.); the difference is Livewire (v3 on Filament 4, v4 on Filament 5), handled entirely by composer constraints — there is no runtime version branching anywhere in `src/` or `tests/`. CI pins each Filament major explicitly via the `filament` matrix axis in `.github/workflows/tests.yml`.

Filament 3 support was dropped; the 1.x branch of this package serves it.

### Test Structure (tests/)

`phpunit.xml` defines three suites: `Components`, `Unit` and `Browser`.

- **`Components/`** — Integration tests for MoneyInput, MoneyEntry, MoneyColumn (uses Livewire test components)
- **`Unit/`** — Unit tests for the service provider, synthesizers, casts, `HasMoneyAttributes`
- **`Browser/`** — Playwright-driven tests via `pest-plugin-browser`, bound to `BrowserTestCase`. Excluded from `composer test`.
- **`Support/`** — `Components/` holds the test Livewire components (`FormTestComponent`, `InfolistTestComponent`, `TableTestComponent`, `BrowserFormComponent`); `Models/`, `Database/` hold the `Post` model, a cast-less variant, the factory and the migration.
- **`Pest.php`** — Global test helpers: `createTestComponent()`, `createFormTestComponent()`, `createInfolistTestComponent()`, `getComponent()`, `getComponentState()`, `validationTester()`, `replaceNonBreakingSpaces()`

### Configuration

Config file: `config/filament-money-field.php`, all keys env-overridable.

Own settings — the value in this file is what applies:
- `MONEY_DEFAULT_LOCALE` — unset follows the Laravel app locale
- `MONEY_USE_INPUT_MASK`, `MONEY_DECIMAL_DIGITS`
- `MONEY_UNIT_PLACEMENT` (`before`/`after`/`hidden`)
- `MONEY_CURRENCY_SWITCHER_ENABLED`

LaraPara overrides — the second half of the file mirrors keys that belong to
`config/larapara.php`: `default_currency`, `intl_currency_symbol`,
`currency_column_suffix`, `available_currencies`, `store.format`. A value set
here wins over LaraPara's; leaving it null — which is also what an absent key or
an absent `MONEY_*` variable gives — leaves LaraPara's own value in charge, so a
published `config/larapara.php` is never silently overridden. The remaining
LaraPara keys (`currency_provider`, `currency_cast_to`, `excluded_currencies`,
`load_crypto_currencies`, `currency_cache`) are only configurable there.

### Code Style

- **Pint**: Laravel preset with aligned binary operators (`pint.json`)
- **PHPStan**: Level 8 with Larastan and php-static-analysis extensions, over `src/` only
- **Rector**: PHP 8.2 target over `config/`, `src/`, `tests/`, with the code quality, coding style, dead code, privatization, type declaration, early return, instanceof and Carbon sets, plus attribute sets. Skips `ClosureToArrowFunctionRector`.

The `check` job in `.github/workflows/code-check.yaml` runs `rector --dry-run`
and fails when anything is left to fix, so apply it locally before pushing. That
job points at `composer fix`, which currently aborts (see above) —
`vendor/bin/rector` is what actually applies the changes.

### Dependencies

- `moneyphp/money` — Core Money value objects
- `pelmered/larapara` — Currency repository, MoneyFormatter (formatting, parsing, rules), Currency value objects, migration macros. Required as `dev-main`, so it tracks that branch rather than a release.
- `filament/support` — Filament framework (`^4.0 | ^5.0`)
- `ext-intl` — Required for locale-aware number formatting

The package floor is PHP 8.2 and Laravel 11 (`illuminate/support: ^11.28 | ^12 | ^13`).
