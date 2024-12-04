<?php

namespace Pelmered\FilamentMoneyField\Infolists\Components;

use Filament\Infolists\Components\TextEntry;
use Pelmered\FilamentMoneyField\Concerns\HasMoneyAttributes;
use Pelmered\FilamentMoneyField\MoneyFormatter;

class MoneyEntry extends TextEntry
{
    use HasMoneyAttributes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isMoney = true;
        $this->numeric();

        $this->formatStateUsing(function (MoneyEntry $component, $state): string {
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
        $this->formatStateUsing(function (MoneyEntry $component, $state) {
            return MoneyFormatter::formatShort(
                $state,
                $component->getCurrency(),
                $component->getLocale(),
                decimals: $this->getDecimals()
            );
        });

        return $this;
    }
}
