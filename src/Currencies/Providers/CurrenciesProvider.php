<?php
namespace Pelmered\FilamentMoneyField\Currencies\Providers;

use PhpStaticAnalysis\Attributes\Type;

interface CurrenciesProvider
{
    #[Type('array<string, array{alphabeticCode: string, currency: string, minorUnit: int, numericCode: int}>')]
    public function loadCurrencies(): array;
}
