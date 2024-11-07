<?php

namespace Pelmered\FilamentMoneyField\MoneyFormatter;

class MoneyFormattingRules
{
    public function __construct(
        public string $currencySymbol,
        public int $fractionDigits,
        public string $decimalSeparator,
        public string $groupingSeparator,
    ) {}
}
