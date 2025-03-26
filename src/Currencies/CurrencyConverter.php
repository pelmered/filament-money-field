<?php

namespace Pelmered\FilamentMoneyField\Currencies;

use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Exchange\SwapExchange;
use Money\Money;
use Swap\Builder;

class CurrencyConverter
{
    private Currency $from;

    private Currency $to;

    // public static function convert(Money $amount, Currency $from, Currency $to)

    public static function from(Currency|string $from)
    {
        $instance = new static;

        $instance->from = static::getCurrency($from);

        return $instance;
    }

    public function to(Currency|string $to)
    {
        $this->to = static::getCurrency($to);

        return $this;
    }

    public function convert(int $amount)
    {
        /*
        $exchange = $this->getSwapExcangeClient();

        $converter = new Converter(new ISOCurrencies, $exchange);

        $from = new Money($amount, $this->from->toMoneyCurrency());

        $usd125 = $converter->convert($from, $this->to->toMoneyCurrency());

        dd($usd125);

        [$usd125, $pair] = $converter->convertAndReturnWithCurrencyPair($eur100, new Currency('USD'));
        */
    }

    private static function getCurrency(Currency|string $currency): Currency
    {
        return $currency instanceof Currency ? $currency : Currency::fromCode($currency);
    }

    public function getSwapExcangeClient()
    {
        $swap = (new Builder)
            // Use the Fixer service as first level provider
            ->add('apilayer_fixer', ['api_key' => config('filament-money-field.conversions.api_keys.fixer')])

            // Use the currencylayer service as first fallback
            // ->add('apilayer_currency_data', ['api_key' => 'Get your key here: https://currencylayer.com'])

            // Use the exchangerates service as second fallback
            // ->add('apilayer_exchange_rates_data', ['api_key' => 'Get your key here: https://exchangeratesapi.io/'])
            ->build();

        return new SwapExchange($swap);
    }
}
