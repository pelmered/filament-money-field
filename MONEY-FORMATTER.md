# MoneyFormatter Documentation

This document describes the public API of the `Pelmered\FilamentMoneyField\MoneyFormatter\MoneyFormatter` class. This class provides static methods for formatting and parsing monetary values according to different locales and formatting rules.

All public methods listed here are considered part of the stable API and adhere to semantic versioning.

## Methods

### `formatMoney`

Formats a `Money\Money` object into a localized currency string.

```php
public static function formatMoney(
    Money\Money $money,
    string $locale,
    int $outputStyle = NumberFormatter::CURRENCY,
    int $decimals = 2,
): string
```

**Parameters:**

-   `$money`: (`Money\Money`) The Money object to format.
-   `$locale`: (`string`) The locale string (e.g., 'en_US', 'sv_SE').
-   `$outputStyle`: (`int`, optional) The `NumberFormatter` style constant (e.g., `NumberFormatter::CURRENCY`, `NumberFormatter::DECIMAL`). Defaults to `NumberFormatter::CURRENCY`.
-   `$decimals`: (`int`, optional) The number of decimal places to display. Defaults to 2. Use negative values for significant digits.

**Returns:**

-   (`string`) The formatted money string.

**Example:**

```php
use Money\Money;
use Money\Currency;
use Pelmered\FilamentMoneyField\MoneyFormatter\MoneyFormatter;

$money = new Money(123456, new Currency('USD'));
echo MoneyFormatter::formatMoney($money, 'en_US'); // Output: $1,234.56

$moneySEK = new Money(123456, new Currency('SEK'));
echo MoneyFormatter::formatMoney($moneySEK, 'sv_SE'); // Output: 1 234,56 kr
```

---

### `format`

Formats a raw numeric value or a `Money\Money` object into a localized string, optionally including the currency symbol.

```php
public static function format(
    null|int|string|Money $value,
    Currency|MoneyCurrency $currency,
    string $locale,
    int $outputStyle = NumberFormatter::CURRENCY,
    int $decimals = 2,
    bool $showCurrencySymbol = true,
): string
```

**Parameters:**

-   `$value`: (`null|int|string|Money\Money`) The value to format. Can be an integer (minor units), a numeric string, a `Money\Money` object, or null/empty string.
-   `$currency`: (`Pelmered\FilamentMoneyField\Currencies\Currency|Money\Currency`) The currency object or code.
-   `$locale`: (`string`) The locale string.
-   `$outputStyle`: (`int`, optional) The `NumberFormatter` style. Defaults to `NumberFormatter::CURRENCY`. See PHP Documentation for [all available styles](https://www.php.net/manual/en/class.numberformatter.php#intl.numberformatter-constants).
-   `$decimals`: (`int`, optional) Number of decimal places. Defaults to 2. Use negative values for significant digits.
-   `$showCurrencySymbol`: (`bool`, optional) Whether to include the currency symbol in the output. Defaults to true.

**Returns:**

-   (`string`) The formatted string, or an empty string if the input value is null or empty.

**Example:**

```php
use Pelmered\FilamentMoneyField\MoneyFormatter\MoneyFormatter;
use Pelmered\FilamentMoneyField\Currencies\Currency;

// Using integer (minor units)
echo MoneyFormatter::format(123456, Currency::fromCode('USD'), 'en_US'); // Output: $1,234.56
echo MoneyFormatter::format(123456, Currency::fromCode('SEK'), 'sv_SE'); // Output: 1 234,56 kr

// Without currency symbol
echo MoneyFormatter::format(123456, Currency::fromCode('USD'), 'en_US', showCurrencySymbol: false); // Output: 1,234.56

// Different decimals
echo MoneyFormatter::format(123456, Currency::fromCode('USD'), 'en_US', decimals: 0); // Output: $1,235
echo MoneyFormatter::format(123456, Currency::fromCode('USD'), 'en_US', decimals: 2); // Output: $1,235.56
echo MoneyFormatter::format(123456, Currency::fromCode('USD'), 'en_US', decimals: -2); // Output: $1,200

// Using Money object
$money = new Money\Money(123456, new Money\Currency('EUR'));
echo MoneyFormatter::format($money, Currency::fromCode('EUR'), 'de_DE'); // Output: 1.234,56 €
```

---

### `numberFormat`

Formats a numeric value into a localized numeric string.

```php
public static function numberFormat(
    null|int|float|string $value,
    string $locale,
    int $decimals = 2,
): string
```

**Parameters:**

-   `$value`: (`null|int|float|string`) The numeric value to format.
-   `$locale`: (`string`) The locale string.
-   `$decimals`: (`int`, optional) Number of decimal places. Defaults to 2. Use negative values for significant digits.
-   `$minorDecimals`: (`int`, optional) Number of minor decimal places. Defaults to 2. Only used for integer values.


**Returns:**

-   (`string`) The formatted number string, or an empty string for non-numeric input.

**Example:**

```php
use Pelmered\FilamentMoneyField\MoneyFormatter\MoneyFormatter;

echo MoneyFormatter::numberFormat(1234.56, 'en_US'); // Output: 1,234.56
echo MoneyFormatter::numberFormat('1234.56', 'en_US'); // Output: 1,234.56
echo MoneyFormatter::numberFormat(123456, 'de_DE'); // Output: 1.234,56
echo MoneyFormatter::numberFormat($money->getAmount(), 'sv_SE'); // Output: 1 234,56
echo MoneyFormatter::numberFormat('non-numeric', 'en_US'); // Output: ''

// Different decimals
echo MoneyFormatter::numberFormat(1234.56, 'en_US', decimals: 0); // Output: 1,235
echo MoneyFormatter::numberFormat(1234.56, 'en_US', decimals: 2); // Output: 1,234.56
echo MoneyFormatter::numberFormat(1234.56, 'en_US', decimals: -2); // Output: 1,200

// Different minor decimals
echo MoneyFormatter::numberFormat(123456, 'en_US', minorDecimals: 0); // Output: 123,456
echo MoneyFormatter::numberFormat(123456, 'en_US', minorDecimals: 2); // Output: 1,234.56
echo MoneyFormatter::numberFormat(123456, 'en_US', decimals: 4, minorDecimals: 4); // Output: 12.3456
```

---

### `formatShort`

Formats a value into a short, abbreviated format (e.g., "1.2K", "$10M"). Useful for displaying large numbers concisely.

```php
public static function formatShort(
    null|int|string|Money $value,
    Currency|MoneyCurrency $currency,
    string $locale,
    int $decimals = 2,
    bool $showCurrencySymbol = true
): string
```

**Parameters:**

-   `$value`: (`null|int|string|Money\Money`) The value to format (minor units, numeric string, or Money object).
-   `$currency`: (`Pelmered\FilamentMoneyField\Currencies\Currency|Money\Currency`) The currency object or code.
-   `$locale`: (`string`) The locale string.
-   `$decimals`: (`int`, optional) Number of decimal places for the abbreviated number. Defaults to 2. Use negative values for significant digits.
-   `$showCurrencySymbol`: (`bool`, optional) Whether to include the currency symbol. Defaults to true.

**Returns:**

-   (`string`) The short-formatted money string. Returns the standard format for values less than 1000 (or 100000 minor units).

**Example:**

```php
use Pelmered\FilamentMoneyField\MoneyFormatter\MoneyFormatter;
use Pelmered\FilamentMoneyField\Currencies\Currency;

// Values >= 100000 minor units (1000) are abbreviated
echo MoneyFormatter::formatShort(123456, Currency::fromCode('USD'), 'en_US'); // Output: $1.23K
echo MoneyFormatter::formatShort(123456789, Currency::fromCode('USD'), 'en_US'); // Output: $1.23M

// Different decimals
echo MoneyFormatter::formatShort(123456789, Currency::fromCode('USD'), 'en_US', decimals: 1); // Output: $1.2M
echo MoneyFormatter::formatShort(123456789, Currency::fromCode('USD'), 'en_US', decimals: 0); // Output: $1M
echo MoneyFormatter::formatShort(123456789, Currency::fromCode('USD'), 'en_US', decimals: -3); // Output: $1.23M

// Without currency symbol
echo MoneyFormatter::formatShort(123456789, Currency::fromCode('USD'), 'en_US', showCurrencySymbol: false); // Output: 1.23M

// Values < 100000 minor units (1000) are not abbreviated
echo MoneyFormatter::formatShort(99999, Currency::fromCode('USD'), 'en_US'); // Output: $999.99
echo MoneyFormatter::formatShort(0, Currency::fromCode('USD'), 'en_US'); // Output: $0.00
```

---

### `parseDecimal`

Parses a localized decimal string into an integer representing minor units (e.g., cents). It removes grouping separators before parsing.

```php
public static function parseDecimal(
    ?string $moneyString,
    Currency|MoneyCurrency $currency,
    string $locale,
    int $decimals = 2
): string
```

**Parameters:**

-   `$moneyString`: (`?string`) The localized decimal string to parse.
-   `$currency`: (`Pelmered\FilamentMoneyField\Currencies\Currency|Money\Currency`) The currency object or code.
-   `$locale`: (`string`) The locale string used for parsing rules (decimal/grouping separators).
-   `$decimals`: (`int`, optional) The expected number of decimal places in the input string. Defaults to 2.

**Returns:**

-   (`string`) The parsed value as an integer string in minor units, or an empty string if input is null/empty.
-   Throws `Money\Exception\ParserException` if the string is not a valid numeric value for the locale.

**Example:**

```php
use Pelmered\FilamentMoneyField\MoneyFormatter\MoneyFormatter;
use Pelmered\FilamentMoneyField\Currencies\Currency;

echo MoneyFormatter::parseDecimal('1,234.56', Currency::fromCode('USD'), 'en_US'); // Output: '123456'
echo MoneyFormatter::parseDecimal('1.234,56', Currency::fromCode('EUR'), 'de_DE'); // Output: '123456'
echo MoneyFormatter::parseDecimal('1 234,56', Currency::fromCode('SEK'), 'sv_SE'); // Output: '123456'
echo MoneyFormatter::parseDecimal('100', Currency::fromCode('USD'), 'en_US');      // Output: '10000'
echo MoneyFormatter::parseDecimal('', Currency::fromCode('USD'), 'en_US');         // Output: ''

try {
    MoneyFormatter::parseDecimal('invalid', Currency::fromCode('USD'), 'en_US');
} catch (Money\Exception\ParserException $e) {
    echo $e->getMessage(); // Output: The value must be a valid numeric value.
}
```

---

### `getFormattingRules`

Retrieves the formatting rules (symbol, separators, digits) for a specific currency and locale.

```php
public static function getFormattingRules(
    string $locale,
    Currency|MoneyCurrency $currency
): Pelmered\FilamentMoneyField\MoneyFormatter\CurrencyFormattingRules
```

**Parameters:**

-   `$locale`: (`string`) The locale string.
-   `$currency`: (`Pelmered\FilamentMoneyField\Currencies\Currency|Money\Currency`) The currency object or code.

**Returns:**

-   (`Pelmered\FilamentMoneyField\MoneyFormatter\CurrencyFormattingRules`) An object containing the formatting rules.

**Example:**

```php
use Pelmered\FilamentMoneyField\MoneyFormatter\MoneyFormatter;
use Pelmered\FilamentMoneyField\Currencies\Currency;

$rules_usd = MoneyFormatter::getFormattingRules('en_US', Currency::fromCode('USD'));
echo $rules_usd->currencySymbol;    // Output: $
echo $rules_usd->decimalSeparator;  // Output: .
echo $rules_usd->groupingSeparator; // Output: ,
echo $rules_usd->fractionDigits;    // Output: 2

$rules_sek = MoneyFormatter::getFormattingRules('sv_SE', Currency::fromCode('SEK'));
echo $rules_sek->currencySymbol;    // Output: kr
echo $rules_sek->decimalSeparator;  // Output: ,
echo $rules_sek->groupingSeparator; // Output:  (non-breaking space)
echo $rules_sek->fractionDigits;    // Output: 2
```

---

### `getDefaultCurrency`

Gets the default currency configured for the application. Reads from `config('filament-money-field.default_currency')` or falls back to `Filament\Infolists\Infolist::$defaultCurrency`.

```php
public static function getDefaultCurrency(): Pelmered\FilamentMoneyField\Currencies\Currency
```

**Parameters:** None.

**Returns:**

-   (`Pelmered\FilamentMoneyField\Currencies\Currency`) The default currency object.

**Example:**

```php
use Pelmered\FilamentMoneyField\MoneyFormatter\MoneyFormatter;

// Assuming default currency is set to 'EUR' in config
$defaultCurrency = MoneyFormatter::getDefaultCurrency();
echo $defaultCurrency->getCode(); // Output: EUR
``` 
