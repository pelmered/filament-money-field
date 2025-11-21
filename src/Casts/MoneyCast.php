<?php

namespace Pelmered\FilamentMoneyField\Casts;

use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Money\Money;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\LaraPara\MoneyFormatter\MoneyFormatter;

class MoneyCast implements StateCast
{
    public function __construct(
        protected MoneyInput $moneyInput,
    ) {}

    /**
     * @return string | array<string, mixed>
     */
    public function get(mixed $state): ?Money {
        if (! $state) {
            return null;
        }

        $currency = $this->moneyInput->getCurrency();
        $amount   = MoneyFormatter::parseDecimal((string) $state, $currency, $this->moneyInput->getLocale(), $this->moneyInput->getDecimals());

        return new Money((int) $amount, $currency->toMoneyCurrency());
    }

    /**
     * @return array<string, mixed>
     */
    public function set(mixed $state): string|int
    {
        if (! $state instanceof Money) {
            return 0;
        }

        return (int) $state->getAmount();
    }
}
