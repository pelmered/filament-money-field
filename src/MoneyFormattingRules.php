<?php
namespace Pelmered\FilamentMoneyField;

class MoneyFormattingRules
{
    public function __construct(
        public string $currencySymbol,
        public int $fractionDigits,
        public string $decimalSeparator,
        public string $groupingSeparator,
    ) {
    }
}
