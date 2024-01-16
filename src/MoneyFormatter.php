<?php
namespace Pelmered\FilamentMoneyField;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;

class MoneyFormatter
{
    public static function format($value, $currency, $locale): string
    {
        $currencies = new ISOCurrencies();
        $numberFormatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        $numberFormatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
        //$numberFormatter->setSymbol(\NumberFormatter::CURRENCY_SYMBOL, $currency);

        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        $money = new Money($value, $currency);

        return $moneyFormatter->format($money);
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
}
