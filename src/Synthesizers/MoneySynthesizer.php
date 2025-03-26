<?php

namespace Pelmered\FilamentMoneyField\Synthesizers;

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
     * @return Currency
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
    public function hydrate(?array $value): ?Money
    {
        if ($value === null) {
            return null;
        }

        return new Money(
            $value['amount'],
            new Currency($value['currency'])
        );
    }
}
