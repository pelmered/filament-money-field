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
        $this->step(0.01);
        $this->minValue = 0;

        $this->formatStateUsing(function (MoneyInput $component, $state): ?string {

            $this->prepare($component);

            if (is_null($state)) {
                return '';
            }
            if(!is_numeric($state)) {
                return $state;
            }

            return MoneyFormatter::decimalToMoneyString($state / 100, $component->getLocale());
        });

        $this->dehydrateStateUsing(function (MoneyInput $component, $state): string {

            $this->prepare($component);

            $state = str_replace(',', '.', $state);

            return (int)($state * 100);
        });
    }

    protected function prepare(MoneyInput $component): void
    {
        $formattingRules = MoneyFormatter::getFormattingRules($component->getLocale());

        $this->prefix($formattingRules->currencySymbol);

        if (config('filament-money-field.use_input_mask')) {
            $this->mask(RawJs::make('$money($input, \'' . $formattingRules->decimalSeparator . '\', \'' . $formattingRules->groupingSeparator . '\', ' . $formattingRules->fractionDigits . ')'));
        }

        $this->stripCharacters($formattingRules->groupingSeparator);
        // OR
        $this->stripCharacters([',', '.', ' ',]);
    }
}
