<?php

namespace Pelmered\FilamentMoneyField\Casts;

use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use Money\Currency;
use Money\Money;

class MoneySynthesizer extends Synth
{
    public static string $key = 'money';

    public static function match(mixed $target): bool
    {
        return $target instanceof Money;
    }

    /**
     * @return array<int, array<string, Currency|string>>
     */
    public function dehydrate(Money $target): array
    {
        return [[
            'amount'   => $target->getAmount(),
            'currency' => $target->getCurrency(),
        ], []];
    }

    /**
     * @param  ?array<string, string>  $value*
     */
    public function hydrate(?array $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value['amount'];

        /*
        return new Money(
            $value['amount'],
            new Currency($value['currency'])
        );
        */
    }
    /*
    public function hydrate($value): ?Money
    {
        if ($value === null) {
            return null;
        }

        return new Money(
            $value['amount'],
            new Currency($value['currency'])
        );
    }
    */

    /*
    public function get(&$target, $key)
    {
        //dd($target, $key);
        return $target->{$key};
    }

    public function set(&$target, $key, $value)
    {
        //dd($target, $key, $value);
        $target->{$key} = $value;
    }
    */
}
