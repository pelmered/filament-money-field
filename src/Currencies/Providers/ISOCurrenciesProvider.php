<?php
namespace Pelmered\FilamentMoneyField\Currencies\Providers;

use PhpStaticAnalysis\Attributes\Type;
use RuntimeException;

class ISOCurrenciesProvider implements CurrenciesProvider
{
    #[Type('array<string, array{alphabeticCode: string, currency: string, minorUnit: int, numericCode: int}>')]
    public function loadCurrencies(): array
    {
        $file = base_path('vendor/moneyphp/money/resources/currency.php');

        if (is_file($file)) {
            return require $file;
        }

        throw new RuntimeException('Failed to load ISO currencies.');
    }
}
