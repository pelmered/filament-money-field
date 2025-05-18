
# Filament Money Field

Money field powered by [Money PHP](https://www.moneyphp.org/en/stable/).

This package gives much better localization support for money fields in Filament than most other packages, and especially the built-in money support on TextColumns and TextEntries. For example when it comes to currency symbols and decimal and thousands separators. Especially for more obscure currencies. This also includes an input field that handles localized formats. 

Example of a money field with Swedish localization.
This package would give "1 234,56 kr", while most other solutions probably would give you something like "SEK 1234.56" which is not the correct format for Sweden.

[![Latest Stable Version](https://poser.pugx.org/pelmered/filament-money-field/v/stable)](https://packagist.org/packages/pelmered/filament-money-field)
[![Total Downloads](https://poser.pugx.org/pelmered/filament-money-field/d/total)](//packagist.org/packages/pelmered/filament-money-field)
[![Monthly Downloads](https://poser.pugx.org/pelmered/filament-money-field/d/monthly)](//packagist.org/packages/pelmered/filament-money-field)
[![License](https://poser.pugx.org/pelmered/filament-money-field/license)](https://packagist.org/packages/pelmered/filament-money-field)

[![Tests](https://github.com/pelmered/filament-money-field/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/pelmered/filament-money-field/actions/workflows/tests.yml)
[![Test Coverage](https://img.shields.io/endpoint?url=https://otterwise.app/badge/github/pelmered/filament-money-field/coverage/25ef865e-5235-4775-a357-246bef38293c)](https://otterwise.app/github/pelmered/filament-money-field)
[![Type Coverage](https://img.shields.io/endpoint?url=https://otterwise.app/badge/github/pelmered/filament-money-field/type/25ef865e-5235-4775-a357-246bef38293c)](https://otterwise.app/github/pelmered/filament-money-field)
[![Complexity](https://img.shields.io/endpoint?url=https://otterwise.app/badge/github/pelmered/filament-money-field/complexity/25ef865e-5235-4775-a357-246bef38293c)](https://otterwise.app/github/pelmered/filament-money-field)
[![Crap](https://img.shields.io/endpoint?url=https://otterwise.app/badge/github/pelmered/filament-money-field/crap/25ef865e-5235-4775-a357-246bef38293c)](https://otterwise.app/github/pelmered/filament-money-field)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat)](https://otterwise.app/github/pelmered/filament-money-field)

[![Tested with Laravel 11 to 12](https://img.shields.io/badge/Tested%20with%20Laravel-11%20%7C%2012-brightgreen?maxAge=2419200)](https://github.com/pelmered/filament-money-field/actions/workflows/tests.yml)
[![Tested on PHP 8.2 to 8.4](https://img.shields.io/badge/Tested%20on%20PHP-8.2%20|%208.3%20|%208.4-brightgreen.svg?maxAge=2419200)](https://github.com/pelmered/filament-money-field/actions/workflows/tests.yml)
[![Tested on OS:es Linux, MacOS, Windows](https://img.shields.io/badge/Tested%20on%20lastest%20versions%20of-%20Ubuntu%20|%20MacOS%20|%20Windows-brightgreen.svg?maxAge=2419200)](https://github.com/pelmered/filament-money-field/actions/workflows/tests.yml)

## Requirements

- PHP 8.2 or higher
- Laravel 11.24.1 or higher
- Filament 3.2 or higher
- [PHP Internationalization extension (intl)](https://www.php.net/manual/en/intro.intl.php)
- The database column type should be a either decimal or integer (amount stored with minor units i.e. cents).

## Key features

- Fully localized money fields in most locales. Report in an issue if you find a locale that is not working as expected.
- Includes fully localized:
  - Input field with currency symbols and (optional) input mask.
  - Column for tables.
  - Entry for infolists.
- Modern and strict tooling. PHP 8.2+, PEST, PHPStan Level 8, Pint, Rector.
- Comprehensive test suite. 100 % type coverage and 99+ % test coverage.
- Configure currency and locale globally or per field.
- Validation rules for valid numeric input, and min/max values.
- A [money formatter class](MONEY-FORMATTER.md) that could be used in your project. Any public method there is considered stable and will not change without major version update. See [documentation](MONEY-FORMATTER.md) for details.

**Are you using this package to make profits? Please consider [sponsoring me](https://github.com/sponsors/pelmered).**

## Installation

```bash
composer require pelmered/filament-money-field
```

## Upgrade to 2.* from 1.*

See [upgrade guide](UPGRADE.md).

## Configuration

### Configure your locale

#### Set the default currency and locale

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

#### Decimals and significant digits

The number of decimals and significant digits can be set in the config file. Defaults to 2.

```env
//with input 123456
MONEY_DECIMAL_DIGITS=0 // Gives 0 decimals, e.g. $1,235
MONEY_DECIMAL_DIGITS=2 // Gives 2 decimals, e.g. $1,234.56
```

For significant digits, use negative values. For example -2 will give you 2 significant digits. 
This is only for displaying the amount. The amount will always be saved with full precision. 

```env
//with input 12345678
MONEY_DECIMAL_DIGITS=-2 // Gives 2 significant digits, e.g. $120,000
MONEY_DECIMAL_DIGITS=-4 // Gives 4 significant digits, e.g. $123,400
```

This can also be set on a per-field basis.

```php
MoneyInput::make('price')->decimals(0);
MoneyEntry::make('price')->decimals(2);
MoneyColumn::make('price')->decimals(-2);
```

### Configuration for saving currency per field in database (Recommended, especially for multi-currency applications) 

#### Migrations

Each money column needs a corresponding currency column with the name `{money_column_name}_currency` 

For new columns
```php
Schema::table('tablename', function (Blueprint $table) {
    $table->money('price'); // This will create two columns, 'price' (integer) and 'price_currency' (char(3))
});
```
For changing existing columns, in this case a column called `price`.
```php
Schema::table('tablename', function (Blueprint $table) {
    $table->char('price_currency', 3)->after('price')->change();
    $this->index(['price', 'price_currency']);
});
```
Available column types(methods) on the Blueprint object are:

| Method            | With int storage        | With decimal storage        |
|-------------------|-------------------------|-----------------------------|
| `money()`         | `BigInteger`            | `Decimal(12, 3)`            |
| `nullableMoney()` | `BigInteger (Nullable)` | `Decimal(12, 3) (Nullable)` |
| `smallMoney()`    | `SmallInteger`          | `Decimal(6, 3)`             |
| `unsignedMoney()` | `BigInteger (Unigned)`  | `Decimal(12, 3) (Unsigned)` |


Don't forget to run your migrations.

#### Casts

Each money column should have a cast that casts the column to a Money object and the currency column should have a cast that casts the column to a Currency object

```php
use Pelmered\Larapara\Casts\CurrencyCast;
use Pelmered\Larapara\Casts\MoneyCast;

protected function casts(): array
{
    return [
        'price' => MoneyCast::class,
        'price_currency' => CurrencyCast::class,
        'another_price' => MoneyCast::class,
        'another_price_currency' => CurrencyCast::class,
    ];
}
```
Or as a property:
```php
protected $casts = [
    'price' => MoneyCast::class,
    'price_currency' => CurrencyCast::class,
    'another_price' => MoneyCast::class,
    'another_price_currency' => CurrencyCast::class,
];
```
This will give you value objects for the money and currency columns when you acceess them in your code. 
For example `$model->price` will get you a `\Money\Money` object. To access the amount you need to write `$model->price->getAmount()`.
Currency columns gives you a `\Pelmered\LaraPara\Currencies\Currency` object, and to get the currency code as a string you need to write `$model->priceCurrency->getCode()`.

Value objects are great in most cases, but if you don't want to use them in your code, you can add an [accessor](https://laravel.com/docs/12.x/eloquent-mutators#accessors-and-mutators) for getting the raw values instead. This will cast your values to strings:
```php
protected function price(): Attribute
{
    return Attribute::make(
        get: static fn (string $value) => $value
    );
}
protected function priceCurrency(): Attribute
{
    return Attribute::make(
        get: static fn (string $value) => $value,
    );
}
````

## Usage

### Form

```php
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;

MoneyInput::make('price'); // Defaults to USD and the current Laravel locale, or what you have set in your .env/config.

MoneyInput::make('price')
    ->currency('USD')
    ->locale('en_US'),

MoneyInput::make('price')
    ->currency('SEK')
    ->locale('sv_SE'),

MoneyInput::make('price')
    ->currency('SEK')
    ->locale('sv_SE')
    ->minValue(0) // Do not allow negative values.
    ->maxValue(10000) // Add min and max value (in minor units, i.e. cents) to the input field. In this case no values over 100
    ->step(100) // Step value for the input field. In this case only multiples of 100 are allowed.
    ->decimals(0)
    ->getSymbolPlacement('after') // Possible options: 'after', 'before', 'none'. Defaults to 'before'
    ->hideCurrencySymbol() // Hide currency symbol.
    ->currencySwitcherEnabled() // Enable the currency switcher (if it is disabled globally in the config file).
    ->currencySwitcherDisabled() // Disable the currency switcher (if it is enabled globally in the config file).
```

### Table column

```php
use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;

MoneyColumn::make('price'); // Defaults to USD and the current Laravel locale, or what you have set in your .env/config.

MoneyColumn::make('price')
    ->currency('USD')
    ->locale('en_US'),

MoneyColumn::make('price')
    ->currency('SEK')
    ->locale('sv_SE'),

MoneyColumn::make('price')
    ->short(), // Short format, e.g. $1.23M instead of $1,234,567.89

MoneyColumn::make('price')
    ->short()
    ->hideCurrencySymbol(), // Short format without currency symbol, e.g. 1.23M instead of $1.23M

MoneyColumn::make('price')
    ->decimals(4)
    ->short(), // $1.2345M

MoneyColumn::make('price')
    ->decimals(-3) // 3 significant digits
    ->short(), // $1.23K or $23.1M
```

### InfoList

```php
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;

MoneyEntry::make('price'); // Defaults to USD and the current Laravel locale, or what you have set in your .env/config.

// The default can be set in the Infolist class with:
public static string $defaultCurrency = 'SEK';

MoneyEntry::make('price')
    ->currency('USD')
    ->locale('en_US'),

MoneyEntry::make('price')
    ->currency('SEK')
    ->locale('sv_SE'),

MoneyEntry::make('price')
    ->short(), // Short fromat, e.g. $1.23M instead of $1,234,567.89
```


## Global Configuration

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

### Placement of currency symbol/code on input fields

Possible options: `after`, `before`, `none`.

```env
MONEY_UNIT_PLACEMENT=after // Defaults to before
```

### Decimals and significant digits

The number of decimals and significant digits can be set in the config file. Defaults to 2.

```env
//with input 123456
MONEY_DECIMAL_DIGITS=0 // Gives 0 decimals, e.g. $1,235
MONEY_DECIMAL_DIGITS=2 // Gives 2 decimals, e.g. $1,234.56
```

For significant digits, use negative values. For example -2 will give you 2 significant digits. 

```env
//with input 12345678
MONEY_DECIMAL_DIGITS=-2 // Gives 2 significant digits, e.g. $120,000
MONEY_DECIMAL_DIGITS=-4 // Gives 4 significant digits, e.g. $123,400
```

This can also be set on a per-field basis.

```php
MoneyInput::make('price')->decimals(0);
MoneyEntry::make('price')->decimals(2);
MoneyColumn::make('price')->decimals(-2);

// You can also pass a callback to the decimals method.
MoneyInput::make('price')->decimals(function () {
    return 0;
});
```

## Roadmap / Ideas for the future

Contact me or create an issue if you want something of this, or something else. 
I appreciate if you could tell me a bit about your use case for that feature as well. 

- Currency conversions. Set what base currency the value in the database is and then convert to the current users preferred currency on the fly. Not sure how edit/create should be handled in this case.

## Contributing

I'm very happy to receive PRs with fixes or improvements. If it is a new feature, it is probably best to open an issue first, so I can give feedback and see if that is something I think would fit in this package. Especially if it is a larger feature, so you don't waste your time.

When you are submitting a PR, I appreciate if you:

- Add tests for your code. Not a strict requirement. Ask for guidance if you are unsure. I will try to help if I have time. 
- Run the test suite and make sure it passes with `composer test`.
- Check the code with `composer lint`. This will run both PHPStan and Pint. See if you can address any issues there before submitting. You might also try to fix the code automatically with `composer fix`.

