<?php

namespace Pelmered\FilamentMoneyField\Synthesizers;

use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use Pelmered\FilamentMoneyField\Currencies\Currency;
use PhpStaticAnalysis\Attributes\Param;
use PhpStaticAnalysis\Attributes\Returns;

class CurrencySynthesizer extends Synth
{
    public static string $key = 'currency';

    public static function match(mixed $target): bool
    {
        return $target instanceof Currency;
    }

    #[Returns('array{string, array{}}')]
    public function dehydrate(Currency $target): array
    {
        return [$target->getCode(), []];
    }

    public function hydrate(?string $value): ?Currency
    {
        if ($value === null) {
            return null;
        }

        return Currency::fromCode($value);
    }
}
