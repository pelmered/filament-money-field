<?php
namespace Pelmered\FilamentMoneyField\Exceptions;

use RuntimeException;

class UnsupportedCurrency extends RuntimeException
{
    public function __construct(string $currencyCode)
    {
        parent::__construct('Currency not supported: ' . $currencyCode);
    }
}
