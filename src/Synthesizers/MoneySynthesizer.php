<?php

namespace Pelmered\FilamentMoneyField\Synthesizers;

use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use Money\Currency;
use Money\Money;
use PhpStaticAnalysis\Attributes\Param;
use PhpStaticAnalysis\Attributes\Returns;

class MoneySynthesizer extends Synth
{
    public static string $key = 'money';

    public static function match(mixed $target): bool
    {
        return $target instanceof Money;
    }

    #[Returns('array{array{amount: string, currency: string}, array{}}')]
    public function dehydrate(Money $target): array
    {
        return [[
            'amount'   => $target->getAmount(),
            'currency' => $target->getCurrency(),
        ], []];
    }

    #[Param(value: '?array{amount?: int|numeric-string, currency?: non-empty-string}')]
    public function hydrate(?array $value): ?Money
    {
        if ($value === null || ! isset($value['amount'], $value['currency'])) {
            return null;
        }

        return new Money(
            $value['amount'],
            new Currency($value['currency'])
        );
    }
}
