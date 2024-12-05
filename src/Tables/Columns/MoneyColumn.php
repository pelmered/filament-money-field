<?php

namespace Pelmered\FilamentMoneyField\Tables\Columns;

use Filament\Tables\Columns\TextColumn;
use Pelmered\FilamentMoneyField\Concerns\HasMoneyAttributes;
use Pelmered\FilamentMoneyField\MoneyFormatter;

class MoneyColumn extends TextColumn
{
    use HasMoneyAttributes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isMoney = true;
        $this->numeric();

        $this->formatStateUsing(function (MoneyColumn $component, null|int|string $state): string {
            return MoneyFormatter::format(
                $state,
                $component->getCurrency(),
                $component->getLocale(),
                decimals: $this->getDecimals()
            );
        });
    }

    public function short($showCurrencySymbol = true): static
    {
        $this->formatStateUsing(function (MoneyColumn $component, null|int|string $state) use ($showCurrencySymbol) {
            return MoneyFormatter::formatShort(
                $state,
                $component->getCurrency(),
                $component->getLocale(),
                decimals: $this->getDecimals(),
                showCurrencySymbol: $showCurrencySymbol,
            );
        });

        return $this;
    }
}
