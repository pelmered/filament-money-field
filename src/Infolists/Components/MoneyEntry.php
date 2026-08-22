<?php

declare(strict_types=1);

namespace Pelmered\FilamentMoneyField\Infolists\Components;

use Filament\Infolists\Components\TextEntry;
use Money\Money;
use Pelmered\FilamentMoneyField\Concerns\HasMoneyAttributes;

class MoneyEntry extends TextEntry
{
    use HasMoneyAttributes;

    protected bool $showCurrencySymbol = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isMoney = true;
        $this->numeric();

        $this->formatStateUsing(function (MoneyEntry $component, Money|int|null $state): string {
            if ($state === null) {
                return '';
            }

            return $component->formatMoney($state, $component->showCurrencySymbol);
        });
    }

    public function short(): static
    {
        $this->formatStateUsing(function (MoneyEntry $component, Money|int|null $state): string {
            if ($state === null) {
                return '';
            }

            return $component->formatMoneyShort($state, $component->showCurrencySymbol);
        });

        return $this;
    }

    public function hideCurrencySymbol(bool $hideCurrencySymbol = true): static
    {
        $this->showCurrencySymbol = ! $hideCurrencySymbol;

        return $this;
    }
}
