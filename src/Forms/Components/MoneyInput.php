<?php

namespace Pelmered\FilamentMoneyField\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Config;
use Pelmered\FilamentMoneyField\hasMoneyAttributes;
use Pelmered\FilamentMoneyField\MoneyFormatter;

class MoneyInput extends TextInput
{
    use hasMoneyAttributes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepare();

        $this->formatStateUsing(function (MoneyInput $component, $state): ?string {

            $this->prepare();

            $currency = $component->getCurrency();
            $locale = $component->getLocale();

            if (is_null($state)) {
                return '';
            }
            if (!is_numeric($state)) {
                return $state;
            }

            return MoneyFormatter::formatAsDecimal($state, $currency, $locale);
        });

        $this->dehydrateStateUsing(function (MoneyInput $component, $state): string {

            $currency = $component->getCurrency();
            $state = MoneyFormatter::parseDecimal($state, $currency, $component->getLocale());

            $this->prepare();

            return $state;
        });
    }

    protected function prepare(): void
    {
        $symbolPlacement = Config::get('filament-money-field.form_currency_symbol_placement', 'before');

        $getCurrencySymbol = function (MoneyInput $component) {
            $formattingRules = MoneyFormatter::getFormattingRules($component->getLocale());
            return $formattingRules->currencySymbol;
        };

        if ($symbolPlacement === 'before') {
            $this->prefix($getCurrencySymbol);
        } else {
            $this->suffix($getCurrencySymbol);
        }

        if (config('filament-money-field.use_input_mask')) {
            $this->mask(function (MoneyInput $component) {
                $formattingRules = MoneyFormatter::getFormattingRules($component->getLocale());
                return RawJs::make('$money($input, \'' . $formattingRules->decimalSeparator . '\', \'' . $formattingRules->groupingSeparator . '\', ' . $formattingRules->fractionDigits . ')');
            });
        }
    }

    public function minValue(mixed $min): static
    {
        $this->rule(static function (MoneyInput $component, mixed $state) use ($min) {
            return function (string $attribute, mixed $value, \Closure $fail) use ($component, $state, $min) {

                $currencyCode = $component->getCurrency();
                $locale       = $component->getLocale();

                $minorValue = MoneyFormatter::parseDecimal(
                    $state,
                    $currencyCode,
                    $locale
                );

                if ($minorValue < $min) {
                    $fail('The :attribute must be greater than or equal to ' . MoneyFormatter::formatAsDecimal($min, $currencyCode, $locale) . '.');
                }
            };
        });

        return $this;
    }

    public function maxValue(mixed $max): static
    {
        $this->rule(static function (MoneyInput $component) use ($max) {
            return function (string $attribute, mixed $value, \Closure $fail) use ($component, $max) {

                $currencyCode = $component->getCurrency();
                $locale       = $component->getLocale();

                $minorValue = MoneyFormatter::parseDecimal(
                    $value,
                    $currencyCode,
                    $locale
                );

                if ($minorValue > $max) {
                    $fail('The :attribute must be less than or equal to ' . MoneyFormatter::formatAsDecimal($max, $currencyCode, $locale) . '.');
                }
            };
        });

        return $this;
    }
}
