<?php

namespace Pelmered\FilamentMoneyField\Tables\Columns;

use Filament\Tables\Columns\TextColumn;
use Pelmered\FilamentMoneyField\hasMoneyAttributes;
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;
use Pelmered\FilamentMoneyField\MoneyFormatter;

class MoneyColumn extends TextColumn
{
    use hasMoneyAttributes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isMoney = true;
        $this->numeric();

        $this->formatStateUsing(static function (MoneyEntry $component, $state): ?string {
            $currency = $component->getCurrency();
            $locale   = $component->getLocale();

            return MoneyFormatter::format($state, $currency, $locale);
        });
    }
}
