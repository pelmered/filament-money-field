# Filament Money Field
Money field powered by [Money PHP ](https://www.moneyphp.org/en/stable/).

This package gives much better localization support for money fields in Filament than most other packages, and especially the built-in money support on TextColumns and TextEntries. For example when it comes to currency symbols and decimal and thousands separators. Especially for more obscure currencies. This also includes an input field that handles localized formats. 

Example of a money field with Swedish localization.
This package would give "1 234,56 kr", while most other solutions probably would give you something like "SEK 1234.56" which is not the correct format for Sweden.

[![Latest Stable Version](https://poser.pugx.org/pelmered/filament-money-field/v/stable)](https://packagist.org/packages/pelmered/filament-money-field)
[![Total Downloads](https://poser.pugx.org/pelmered/filament-money-field/d/total)](//packagist.org/packages/pelmered/filament-money-field)
[![Monthly Downloads](https://poser.pugx.org/pelmered/filament-money-field/d/monthly)](//packagist.org/packages/pelmered/filament-money-field)
[![License](https://poser.pugx.org/pelmered/filament-money-field/license)](https://packagist.org/packages/pelmered/filament-money-field)

[![Tests](https://github.com/pelmered/filament-money-field/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/pelmered/filament-money-field/actions/workflows/tests.yml)
[![Build Status](https://scrutinizer-ci.com/g/pelmered/filament-money-field/badges/build.png?b=main)](https://scrutinizer-ci.com/g/pelmered/filament-money-field/build-status/main)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/pelmered/filament-money-field/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/pelmered/filament-money-field/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/pelmered/filament-money-field/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/pelmered/filament-money-field/?branch=main)

[![Tested on PHP 8.2 to 8.3](https://img.shields.io/badge/tested%20on-PHP%208.2%20|%208.3-brightgreen.svg?maxAge=2419200)](https://github.com/pelmered/filament-money-field/actions/workflows/tests.yml)
[![Tested on OS:es Linux, MacOS, Windows](https://img.shields.io/badge/Tested%20on%20lastest%20versions%20of-%20Linux%20|%20MacOS%20|%20Windows-brightgreen.svg?maxAge=2419200)](https://github.com/pelmered/filament-money-field/actions/workflows/tests.yml)

## Requirements

- PHP 8.2 or higher
- Filament 3.0 or higher
- [PHP Internationalization extension (intl) ](https://www.php.net/manual/en/intro.intl.php)
- The database column should be a integers with minor units (i.e. cents) and not a float (Floats should never be used for storing money).

## Key features

- Fully localized money fields in most locales. Report in an issue if you find a locale that is not working as expected.
- Includes fully localized:
  - Input field with currency symbols and (optional) input mask.
  - Column for tables.
  - Entry for infolists.
- Comprehensive test suite.
- Configure currency and locale globally or per field.
- Validation rules for valid numeric input, and min/max values.
- A [money formatter class](https://github.com/pelmered/filament-money-field/blob/main/src/MoneyFormatter.php) that could be used in your project.

## Installation

```bash
composer require pelmered/filament-money-field
```

## Configure your locale

### Set the default currency and locale
**Set the default options for currency and locale so that you don't have to set them for every field.**

**Option 1 (Recommended): Put the default options in your .env file.**

```env
MONEY_DEFAULT_LOCALE=sv_SE
MONEY_DEFAULT_CURRENCY=SEK
```
**Option 2: Publish the config file and set the default options there.**
```bash
php artisan vendor:publish --provider="Pelmered\FilamentMoneyField\FilamentMoneyFieldServiceProvider" --tag="config"
```

## Additional Configuration

### If you want to use the formatting mask on the `MoneyInput` component 
**This will auto format the input field as you type.**

This is a bit experimental at the moment and is therefore disabled by default. Hopefully it will be improved in the future and enabled by default in the next major version. Please try it out and provide feedback.
```env
MONEY_USE_INPUT_MASK=true // Defaults to false
```

### Use international currency codes (ISO 4217)

If you want to use international currency codes istead of their symbols or local short variants. For example USD instead of $, EUR instead of € or SEK instead of kr. 

```env
MONEY_INTL_CURRENCY_SYMBOL=true // Defaults to false
```

### Placement of currency symbol/code on input fields. 

Possible options: `after`, `before`, `none`.

```env
MONEY_UNIT_PLACEMENT=after // Defaults to before
```

## Usage

### InfoList

```php
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;

MoneyEntry::make('price'); // Defaults to USD and the current Laravel locale, or what you have set in your .env/config.

// The default can be set in the Infolist class with:
public static string $defaultCurrency = 'SEK';

MoneyEntry::make('price')
    ->currency('USD')
    ->locale('en_US');

MoneyEntry::make('price')
    ->currency('SEK')
    ->locale('sv_SE');
```

### Form

```php
use Filament\Forms\Components\MoneyField;

MoneyInput::make('price'); // Defaults to USD and the current Laravel locale, or what you have set in your .env/config.

MoneyInput::make('price')
    ->currency('USD')
    ->locale('en_US');

MoneyInput::make('price')
    ->currency('SEK')
    ->locale('sv_SE');


MoneyInput::make('price')
    ->currency('SEK')
    ->locale('sv_SE')
    ->minValue(0) // Do not allow negative values.
    ->maxValue(10000); // Add min and max value (in minor units, i.e. cents) to the input field. In this case no values over 100
```

### Table column

```php
use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;

MoneyColumn::make('price'); // Defaults to USD and the current Laravel locale, or what you have set in your .env/config.

MoneyColumn::make('price')
    ->currency('USD')
    ->locale('en_US');

MoneyColumn::make('price')
    ->currency('SEK')
    ->locale('sv_SE');
```

## Roadmap / Ideas for the future. 

Contact me or create an issue if you want something of this, or something else. 
I appreciate if you could tell me a bit about your use case for that feature as well. 

- Add support for dynamic currency and locale based on current user.
- Currency conversions. Set what base currency the value in the database is and then convert to the current users preferred currency on the fly. Not sure how edit/create should be handled in this case. 

## Contributing

I'm very happy to receive PRs with fixes or improvements. If it is a new feature, it is probably best to open an issue first, so I can give feedback and see if that is something I think would fit in this package. Especially if it is a larger feature, so you don't waste your time.

When you are submitting a PR, I appreciate if you:

- Add tests for your code. Not a strict requirement. Ask for guidance if you are unsure. I will try to help if I have time. 
- Run the test suite and make sure it passes with `composer test`.
- Check the code with `composer phpstan`. It doesn't have to be 100 % clean, but if there is something there in your code it is good if you can address it before submitting. 
