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

        $f = dirname($file);

        for($i = 5; $i > 1; $i--) {
            dump(scandir(dirname($f, $i)));
        }

        //$files1 = scandir(dirname($f, 2));
        $files2 = scandir(dirname($f, 5));

        dump($f, is_dir($f), $files2);

        if (is_file($file)) {
            return require $file;
        }

        throw new RuntimeException('Failed to load ISO currencies.');
    }
}
