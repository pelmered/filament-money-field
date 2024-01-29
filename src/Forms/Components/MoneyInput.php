<?php

namespace Pelmered\FilamentMoneyField\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Pelmered\FilamentMoneyField\hasMoneyAttributes;
use Pelmered\FilamentMoneyField\MoneyFormatter;

class MoneyInput extends TextInput
{
    use hasMoneyAttributes;

    protected function setUp(): void
    {
        parent::setUp();


        $this->inputMode('decimal');
        $this->rule('numeric');
        $this->step(0.01);
        $this->minValue = 0;

        $this->formatStateUsing(function (MoneyInput $component, $state): ?string {

            $formattingRules = MoneyFormatter::getFormattingRules($component->getLocale());

            $this->prefix($formattingRules->currencySymbol);

            if (config('filament-money-field.use_input_mask')) {
                $this->mask(RawJs::make('$money($input, \'' . $formattingRules->decimalSeparator . '\', \'' . $formattingRules->groupingSeparator . '\', ' . $formattingRules->fractionDigits . ')'));
            }
            
            $this->stripCharacters($formattingRules->groupingSeparator);

            return is_null($state) ? null : MoneyFormatter::decimalToMoneyString($state / 100, $component->getLocale());
        });

        $this->dehydrateStateUsing(static function (MoneyInput $component, $state): string {
            $formattingRules = MoneyFormatter::getFormattingRules($component->getLocale());

            if ($formattingRules->decimalSeparator === ',') {
                $state = str_replace(',', '.', $state);
            }

            return (int)($state * 100);
        });
    }
}
