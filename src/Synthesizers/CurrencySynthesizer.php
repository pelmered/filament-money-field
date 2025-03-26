<?php

namespace Pelmered\FilamentMoneyField\Synthesizers;

use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use Pelmered\FilamentMoneyField\Currencies\Currency;

class CurrencySynthesizer extends Synth
{
    public static string $key = 'currency';

    public static function match(mixed $target): bool
    {
        return $target instanceof Currency;
    }

    /**
     * @return Currency
     */
    public function dehydrate(Currency $target): array
    {
        return [$target->getCode(), []];
    }

    /**
     * @param  ?array<string, string>  $value*
     */
    public function hydrate(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return Currency::fromCode($value);
    }
}
