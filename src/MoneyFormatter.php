<?php
namespace Pelmered\FilamentMoneyField;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Parser\IntlLocalizedDecimalParser;
use Money\Money;
use Money\Currency;
use NumberFormatter;

class MoneyFormatter
{
    public static function format($value, $currency, $locale): string
    {
        $currencies = new ISOCurrencies();
        $numberFormatter = self::getNumberFormatter($locale, NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        $money = new Money($value, $currency);

        return $moneyFormatter->format($money);
    }

    public static function parseDecimal($moneyString, $currency, $locale): string
    {
        $currencies = new ISOCurrencies();
        $numberFormatter = self::getNumberFormatter($locale, NumberFormatter::DECIMAL);
        $moneyParser = new IntlLocalizedDecimalParser($numberFormatter, $currencies);

        $money = $moneyParser->parse($moneyString, new Currency($currency));

        return $money->getAmount();
    }

    public static function getFormattingRules($locale): MoneyFormattingRules
    {
        $numberFormatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        return new MoneyFormattingRules(
            currencySymbol: $numberFormatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL),
            fractionDigits: $numberFormatter->getAttribute(NumberFormatter::FRACTION_DIGITS),
            decimalSeparator: $numberFormatter->getSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL),
            groupingSeparator: $numberFormatter->getSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL),
        );
    }

    public static function decimalToMoneyString($moneyString, $locale): string
    {
        $formattingRules = self::getFormattingRules($locale);
        $moneyString = (string) $moneyString;

        if($formattingRules->decimalSeparator === ',') {
            $moneyString = str_replace('.', ',', (string) $moneyString);
        }

        return $moneyString;
    }

    private static function getNumberFormatter($locale, int $style): NumberFormatter
    {
        $numberFormatter = new NumberFormatter($locale, $style);
        $numberFormatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

        return $numberFormatter;
    }
}
