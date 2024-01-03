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

    public static function getCurrencySymbol($currency, $locale): string
    {
        return (new NumberFormatter($locale, NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
    }
}
