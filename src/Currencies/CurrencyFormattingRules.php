<?php
namespace Pelmered\FilamentMoneyField\Currencies;

class CurrencyFormattingRules
{
    public function __construct(
        public string $code,
        ?int $minorUnit = null,
        $placement = null

    ) {
        $this->code = strtoupper($code);


        $this->formattingRules = CurrencyFormattingRules::fromCode($code, $minorUnit);
    }
}
