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
