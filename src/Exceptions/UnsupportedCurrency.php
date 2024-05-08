<?php
namespace Pelmered\FilamentMoneyField\Exceptions;

class UnsupportedCurrency extends \RuntimeException
{
    public function __construct(string $currencyCode)
    {
        parent::__construct('Currency not supported: ' . $currencyCode);
    }
}
