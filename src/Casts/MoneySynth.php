<?php
namespace Pelmered\FilamentMoneyField\Casts;

use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use Money\Currency;
use Money\Money;

class MoneySynth extends Synth
{
    public static $key = 'money';

    public static function match($target)
    {
        return $target instanceof Money;
    }

    public function dehydrate($target)
    {
        return [[
            'amount'   => $target->getAmount(),
            'currency' => $target->getCurrency(),
        ], []];
    }

    public function hydrate($value)
    {
        if($value === null) {
            return null;
        }

        return new Money(
            $value['amount'],
            new Currency($value['currency'])
        );
    }
}
