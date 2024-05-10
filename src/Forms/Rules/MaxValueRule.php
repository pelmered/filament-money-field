<?php
namespace Pelmered\FilamentMoneyField\Forms\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Money\Exception\ParserException;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\MoneyFormatter;

readonly class MaxValueRule implements ValidationRule
{
    public function __construct(private int $max, private MoneyInput $component)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $currencyCode = $this->component->getCurrency();
        $locale       = $this->component->getLocale();

        try {
            $minorValue = MoneyFormatter::parseDecimal(
                $value,
                $currencyCode,
                $locale
            );

            if ($minorValue >= $this->max) {
                $fail(
                    strtr(
                        'The {attribute} must be less than or equal to {value}.',
                        [
                            '{attribute}' => Str::of($this->component->getLabel())->title(),
                            '{value}' => MoneyFormatter::formatAsDecimal($this->max, $currencyCode, $locale),
                        ]
                    )
                );
            }
        } catch (ParserException) {
            $fail(
                strtr(
                    'The {attribute} must be a valid numeric value.',
                    [
                        '{attribute}' => Str::title($this->component->getLabel()),
                    ]
                )
            );
        }
    }
}
