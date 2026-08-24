<?php

declare(strict_types=1);

namespace Pelmered\FilamentMoneyField\Tables\Columns;

use Filament\Tables\Columns\TextColumn;
use Money\Money;
use Pelmered\FilamentMoneyField\Concerns\HasMoneyAttributes;

class MoneyColumn extends TextColumn
{
    use HasMoneyAttributes;

    protected bool $showCurrencySymbol = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isMoney = true;
        $this->numeric();

        $this->formatStateUsing(function (MoneyColumn $component, Money|int|string|null $state): string {
            if ($state === null) {
                return '';
            }

            return $component->formatMoney($state, $component->showCurrencySymbol);
        });
    }

    public function short(): static
    {
        $this->formatStateUsing(function (MoneyColumn $component, Money|int|string|null $state): string {
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
