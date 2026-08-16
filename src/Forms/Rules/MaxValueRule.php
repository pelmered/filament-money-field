<?php

namespace Pelmered\FilamentMoneyField\Forms\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Money\Exception\ParserException;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\LaraPara\MoneyFormatter\MoneyFormatter;

readonly class MaxValueRule implements ValidationRule
{
    public function __construct(private int $max, private MoneyInput $component) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $currencyCode = $this->component->getCurrency();
        $locale       = $this->component->getLocale();

        try {
            $minorValue = (int) MoneyFormatter::parseDecimal(
                $value,
                $currencyCode,
                $locale
            );

            if ($minorValue > $this->max) {
                $fail('filament-money-field::validation.max_value')
                    ->translate([
                        'attribute' => $this->component->getValidationAttribute(),
                        'value'     => MoneyFormatter::numberFormat($this->max, $locale),
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
