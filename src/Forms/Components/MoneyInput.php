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

    /**
     * The name of the component.
     * 
     * @var string
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->prepare($this);

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

            $currency = $component->getCurrency();
            $state = MoneyFormatter::parseDecimal($state, $currency, $component->getLocale());

            $this->prepare($component);

            return $state;
        });
    }

    /**
     * Prepare the money input.
     * 
     * @param MoneyInput $component
     * @return void
     */
    protected function prepare(MoneyInput $component): void
    {
        $formattingRules = MoneyFormatter::getFormattingRules($component->getLocale());
        $symbolPlacement = Config::get('filament-money-field.form_currency_symbol_placement', 'before');

        if ($symbolPlacement === 'before') {
            $this->prefix($formattingRules->currencySymbol);
        } else {
            $this->suffix($formattingRules->currencySymbol);
        }

        if (config('filament-money-field.use_input_mask')) {
            $this->mask(RawJs::make('$money($input, \'' . $formattingRules->decimalSeparator . '\', \'' . $formattingRules->groupingSeparator . '\', ' . $formattingRules->fractionDigits . ')'));
        }
    }

    /**
     * Set the minimum value of the money input.
     * 
     * @param mixed $min
     * @return static
     */
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

    /**
     * Set the maximum value of the money input.
     * 
     * @param mixed $max
     * @return static
     */
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
