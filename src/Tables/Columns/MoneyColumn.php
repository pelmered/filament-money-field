<?php

namespace Pelmered\FilamentMoneyField\Tables\Columns;

use Filament\Tables\Columns\TextColumn;
use Pelmered\FilamentMoneyField\HasMoneyAttributes;
use Pelmered\FilamentMoneyField\MoneyFormatter;

class MoneyColumn extends TextColumn
{
    use HasMoneyAttributes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isMoney = true;
        $this->numeric();

        $this->formatStateUsing(function (MoneyColumn $column, $state): string {
            return MoneyFormatter::format($state, $column->getCurrency(), $column->getLocale());
        });
    }

    public function short()
    {
        $this->formatStateUsing(function (MoneyColumn $component, $state) {
            return MoneyFormatter::formatShort($state, $component->getCurrency(), $component->getLocale());
        });

        return $this;
    }
}
