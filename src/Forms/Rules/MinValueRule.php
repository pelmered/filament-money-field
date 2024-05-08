<?php
namespace Pelmered\FilamentMoneyField\Forms\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\MoneyFormatter;

readonly class MinValueRule implements ValidationRule
{
    public function __construct(private int $min, private MoneyInput $component)
    {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $currencyCode = $this->component->getCurrency();
        $locale       = $this->component->getLocale();

        $minorValue = MoneyFormatter::parseDecimal(
            $value,
            $currencyCode,
            $locale
        );

        if ($minorValue <= $this->min) {
            $fail('The ' . $this->component->getLabel() . ' must be less than or equal to ' . MoneyFormatter::formatAsDecimal($this->min, $currencyCode, $locale) . '.');
        }
    }
}
