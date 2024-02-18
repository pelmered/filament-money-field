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

        $this->formatStateUsing(function (MoneyInput $component, $state): ?string {
            
            $this->prepare($component);

            $currency = $component->getCurrency();
            $locale = $component->getLocale();

            if (is_null($state)) {
                return '';
            }
            if(!is_numeric($state)) {
                return $state;
            }

            return MoneyFormatter::formatAsDecimal($state, $currency, $locale);
        });

        $this->dehydrateStateUsing(function (MoneyInput $component, $state): string {

            $currency = $component->getCurrency()->getCode();
            $state = MoneyFormatter::parseDecimal($state, $currency, $component->getLocale());

            $this->prepare($component);

            return $state;
        });
    }

    protected function prepare(MoneyInput $component): void
    {
        $formattingRules = MoneyFormatter::getFormattingRules($component->getLocale());
        $this->prefix($formattingRules->currencySymbol);

        if (config('filament-money-field.use_input_mask')) {
            $this->mask(RawJs::make('$money($input, \'' . $formattingRules->decimalSeparator . '\', \'' . $formattingRules->groupingSeparator . '\', ' . $formattingRules->fractionDigits . ')'));
        }
    }

    public function minValue(mixed $min): static
    {
        $this->rule(static function (MoneyInput $component, mixed $state) use ($min) {
            return function (string $attribute, mixed $value, \Closure $fail) use ($component, $state, $min) {

                $value = MoneyFormatter::parseDecimal(
                    $state, 
                    $component->getCurrency()->getCode(),
                    $component->getLocale()
                );

                if ($value < $min) {
                    $fail('The :attribute must be greater than or equal to ' . $min . '.');
                }
            };
        });

        return $this;
    }

    public function maxValue(mixed $max): static
    {
        $this->rule(static function (MoneyInput $component, mixed $state) use ($max) {
            return function (string $attribute, mixed $value, \Closure $fail) use ($component, $state, $max) {

                $value = MoneyFormatter::parseDecimal(
                    $state, 
                    $component->getCurrency()->getCode(),
                    $component->getLocale()
                );

                if ($value > $max) {
                    $fail('The :attribute must be less than or equal to ' . $max . '.');
                }
            };
        });

        return $this;
    }
}
