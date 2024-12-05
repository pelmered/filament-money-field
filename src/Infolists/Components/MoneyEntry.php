<?php

namespace Pelmered\FilamentMoneyField\Infolists\Components;

use Filament\Infolists\Components\TextEntry;
use Pelmered\FilamentMoneyField\Concerns\HasMoneyAttributes;
use Pelmered\FilamentMoneyField\MoneyFormatter;

class MoneyEntry extends TextEntry
{
    use HasMoneyAttributes;

    protected bool $showCurrencySymbol = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isMoney = true;
        $this->numeric();

        $this->formatStateUsing(function (MoneyEntry $component, null|int|string $state): string {
            return MoneyFormatter::format(
                $state,
                $component->getCurrency(),
                $component->getLocale(),
                decimals: $this->getDecimals()
            );
        });
    }

    public function short(): static
    {
        $this->formatStateUsing(function (MoneyEntry $component, null|int|string $state) {
            return MoneyFormatter::formatShort(
                $state,
                $component->getCurrency(),
                $component->getLocale(),
                decimals: $this->getDecimals(),
                showCurrencySymbol: $component->showCurrencySymbol,
            );
        });

        return $this;
    }

    public function hideCurrencySymbol(bool $hideCurrencySymbol = true): static
    {
        $this->showCurrencySymbol = ! $hideCurrencySymbol;

        return $this;
    }
}
