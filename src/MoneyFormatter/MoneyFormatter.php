<?php

namespace Pelmered\FilamentMoneyField\MoneyFormatter;

use Filament\Infolists\Infolist;
use Illuminate\Support\Number;
use Money\Currencies\ISOCurrencies;
use Money\Currency as MoneyCurrency;
use Money\Exception\ParserException;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use Money\Parser\IntlLocalizedDecimalParser;
use NumberFormatter;
use Pelmered\FilamentMoneyField\Currencies\Currency;

class MoneyFormatter
{
    public static function formatMoney(
        Money $money,
        string $locale,
        int $outputStyle = NumberFormatter::CURRENCY,
        int $decimals = 2,
    ): string {
        $numberFormatter = self::getNumberFormatter($locale, $outputStyle, $decimals);
        $moneyFormatter  = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies);

        return $moneyFormatter->format($money);  // Outputs something like "$1.234,56"
    }

    public static function format(
        null|int|string|Money $value,
        Currency|MoneyCurrency $currency,
        string $locale,
        int $outputStyle = NumberFormatter::CURRENCY,
        int $decimals = 2,
    ): string {
        if ($value === '' || $value === null) {
            return '';
        }

        $money = $value instanceof Money
            ? $value
            : new Money((int) $value, $currency instanceof Currency ? $currency->toMoneyCurrency() : $currency);

        return static::formatMoney($money, $locale, $outputStyle, $decimals);
    }

    public static function formatAsDecimal(
        null|int|string|Money $value,
        Currency|MoneyCurrency $currency,
        string $locale,
        int $decimals = 2,
    ): string {
        if ($value === null) {
            return '';
        }

        if ($value instanceof Money) {
            $currency = $value->getCurrency();
            $value    = $value->getAmount();
        }

        return static::format($value, $currency, $locale, NumberFormatter::DECIMAL, $decimals);
    }

    public static function numberFormat(
        null|int|float|string $value,
        string $locale,
        int $decimals = 2,
    ): string {
        if (! is_numeric($value)) {
            return '';
        }

        $numberFormatter = self::getNumberFormatter($locale, NumberFormatter::DECIMAL, $decimals);

        return (string) $numberFormatter->format((float) $value);  // Outputs something like "1.234,56"
    }

    public static function formatShort(
        null|int|string|Money $value,
        Currency|MoneyCurrency $currency,
        string $locale,
        int $decimals = 2,
        bool $showCurrencySymbol = true
    ): string {
        if (! is_numeric($value)) {
            return '';
        }

        if ($value === 0) {
            return static::format(0, $currency, $locale, decimals: $decimals);
        }

        // No need to abbreviate if the value is less than 1000
        if ($value < 100000) {
            if (! $showCurrencySymbol) {
                return static::numberFormat((int) $value / 100, $locale, decimals: $decimals);
            }

            return static::format($value, $currency, $locale, $decimals);
        }

        $abbreviated = (string) Number::abbreviate((int) $value / 100, 0, abs($decimals));

        // Split the number and the suffix
        if (preg_match('/^(?<number>[0-9.]+)(?<suffix>[A-Z])$/', $abbreviated, $matches1) !== 1) {
            throw new \RuntimeException('Invalid format');
        }

        $abbreviatedNumber = (float) $matches1['number'];
        $suffix            = $matches1['suffix'];

        $formattedNumber = static::numberFormat($abbreviatedNumber, $locale, decimals: $decimals);

        if (! $showCurrencySymbol) {
            return $formattedNumber.$suffix;
        }

        // Format the number
        $formattedCurrency = static::format((int) ($abbreviatedNumber * 100), $currency, $locale, decimals: $decimals);

        // Find the formatted number
        if (preg_match('/(?<number>[0-9\.,]+)/', $formattedCurrency, $matches2) !== 1) {
            throw new \RuntimeException('Invalid format');
        }

        return str_replace($matches2['number'], $formattedNumber.$suffix, $formattedCurrency);
    }

    public static function parseDecimal(
        ?string $moneyString,
        Currency|MoneyCurrency $currency,
        string $locale,
        int $decimals = 2
    ): string {
        if (is_null($moneyString) || $moneyString === '') {
            return '';
        }

        $currency = $currency instanceof Currency ? $currency->toMoneyCurrency() : $currency;

        $numberFormatter = self::getNumberFormatter($locale, NumberFormatter::DECIMAL, $decimals);
        $moneyParser     = new IntlLocalizedDecimalParser($numberFormatter, new ISOCurrencies);

        // Remove grouping separator from the money string
        // This is needed to fix some parsing issues with small numbers such as
        // "2,00" with "," left as thousands separator in the wrong place
        // See: https://github.com/pelmered/filament-money-field/issues/20
        $formattingRules = self::getFormattingRules($locale, $currency);
        $moneyString     = str_replace($formattingRules->groupingSeparator, '', $moneyString);

        try {
            return $moneyParser->parse($moneyString, $currency)->getAmount();
        } catch (ParserException) {
            throw new ParserException('The value must be a valid numeric value.');
        }
    }

    public static function getFormattingRules(string $locale, Currency|MoneyCurrency $currency): CurrencyFormattingRules
    {
        $config          = config('filament-money-field');
        $numberFormatter = new NumberFormatter($locale.'@currency='.$currency->getCode(), NumberFormatter::CURRENCY);

        return new CurrencyFormattingRules(
            currencySymbol: $numberFormatter->getSymbol(
                $config['intl_currency_symbol']
                    ? NumberFormatter::INTL_CURRENCY_SYMBOL
                    : NumberFormatter::CURRENCY_SYMBOL
            ),
            fractionDigits: $numberFormatter->getAttribute(NumberFormatter::FRACTION_DIGITS),
            decimalSeparator: $numberFormatter->getSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL),
            groupingSeparator: $numberFormatter->getSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL),
        );
    }

    public static function getDefaultCurrency(): Currency
    {
        $defaultCurrencyCode = (string) (config('filament-money-field.default_currency') ?? Infolist::$defaultCurrency);

        return Currency::fromCode($defaultCurrencyCode);
    }

    private static function getNumberFormatter(string $locale, int $style, int $decimals = 2): NumberFormatter
    {
        $config = config('filament-money-field');

        $numberFormatter = new NumberFormatter($locale, $style);

        if ($decimals < 0) {
            $numberFormatter->setAttribute(NumberFormatter::MAX_SIGNIFICANT_DIGITS, abs($decimals));
        } else {
            $numberFormatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);
        }

        if ($config['intl_currency_symbol']) {
            $intlCurrencySymbol = $numberFormatter->getSymbol(NumberFormatter::INTL_CURRENCY_SYMBOL);
            if ($numberFormatter->getTextAttribute(NumberFormatter::POSITIVE_PREFIX) !== '') {
                // "\xc2\xa0" is a non-breaking space
                $numberFormatter->setTextAttribute(NumberFormatter::POSITIVE_PREFIX, $intlCurrencySymbol."\xc2\xa0");
            }

            if ($numberFormatter->getTextAttribute(NumberFormatter::POSITIVE_SUFFIX) !== '') {
                // "\xc2\xa0" is a non-breaking space
                $numberFormatter->setTextAttribute(NumberFormatter::POSITIVE_SUFFIX, "\xc2\xa0".$intlCurrencySymbol);
            }
        }

        return $numberFormatter;
    }
}
