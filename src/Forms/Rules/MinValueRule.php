<?php

namespace Pelmered\FilamentMoneyField\Forms\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Money\Exception\ParserException;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\LaraPara\Exceptions\UnsupportedCurrency;
use Pelmered\LaraPara\MoneyFormatter\MoneyFormatter;

readonly class MinValueRule implements ValidationRule
{
    public function __construct(private int $min, private MoneyInput $component) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $currencyCode = $this->component->getCurrency();
        } catch (UnsupportedCurrency) {
            // The record holds a currency code this configuration does not know. Resolving it
            // is what the field itself does to render, so that misconfiguration is already
            // loud where it belongs; raising it again from here would turn a save into a 500,
            // and reporting it as a failed bound would blame an amount that is fine.
            return;
        }

        $locale = $this->component->getLocale();

        try {
            $minorValue = (int) MoneyFormatter::parseToMinor(
                $value,
                $currencyCode,
                $locale
            );

            if ($minorValue < $this->min) {
                $fail('filament-money-field::validation.min_value')
                    ->translate([
                        'attribute' => $this->component->getValidationAttribute(),
                        'value'     => MoneyFormatter::formatFromMinor($this->min, $currencyCode, $locale, showCurrencySymbol: false),
                    ]);
            }
        } catch (ParserException) {
            $fail('filament-money-field::validation.numeric_value')
                ->translate([
                    'attribute' => $this->component->getValidationAttribute(),
                ]);
        }
    }
}
