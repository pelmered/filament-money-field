<?php

namespace Pelmered\FilamentMoneyField\Tables\Columns;

use Filament\Tables\Columns\TextColumn;
use Money\Money;
use Pelmered\FilamentMoneyField\Concerns\HasMoneyAttributes;
use Pelmered\FilamentMoneyField\MoneyFormatter\MoneyFormatter;

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

            $amount = $state instanceof \Money\Money ? $state->getAmount() : $state;

            return MoneyFormatter::format(
                $amount,
                $component->getCurrency(),
                $component->getLocale(),
                decimals: $this->getDecimals()
            );
        });
    }

    public function short(): static
    {
        $this->formatStateUsing(function (MoneyColumn $component, Money|int|string|null $state): string {
            if ($state === null) {
                return '';
            }

            $amount = $state instanceof \Money\Money ? $state->getAmount() : $state;

            return MoneyFormatter::formatShort(
                $amount,
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
