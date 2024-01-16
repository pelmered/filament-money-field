<?php

namespace Pelmered\FilamentMoneyField\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;
use Pelmered\FilamentMoneyField\hasMoneyAttributes;
use Pelmered\FilamentMoneyField\MoneyFormatter;

class MoneyInput extends TextInput
{
    use hasMoneyAttributes;

    protected function setUp(): void
    {
        parent::setUp();

        $formattingRules = MoneyFormatter::getFormattingRules($this->getLocale());

        $this->prefix($formattingRules->currencySymbol);

        //$this->mask(RawJs::make('$money($input, \'' . $formattingRules->decimalSeparator . '\', \'' . $formattingRules->groupingSeparator . '\', '.$formattingRules->fractionDigits.')'));
        $this->stripCharacters($formattingRules->groupingSeparator);
        $this->inputMode('decimal');
        $this->rule('numeric');
        $this->step(0.01);
        $this->minValue = 0;

        /*
        $this->afterStateHydrated(static function (MoneyInput $component, $state): void {
           $component->state($state/100);
        });
        */

        $this->formatStateUsing(fn (string $state): string => (int) $state/100);

        $this->dehydrateStateUsing(static function (MoneyInput $component, $state): string {

            $formattingRules = MoneyFormatter::getFormattingRules($component->getLocale());

            if($formattingRules->decimalSeparator === ',') {
                $state = str_replace(',', '.', $state);
            }

            return (int) ($state*100);
        });

    }
}
