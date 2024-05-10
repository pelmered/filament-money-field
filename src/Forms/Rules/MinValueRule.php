<?php
namespace Pelmered\FilamentMoneyField\Forms\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Money\Exception\ParserException;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\MoneyFormatter;

readonly class MinValueRule implements ValidationRule
{
    public function __construct(private int $min, private MoneyInput $component)
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

            if ($minorValue <= $this->min) {
                $fail(
                    strtr(
                        'The {attribute} must be at least {value}.',
                        [
                            '{attribute}' => ucwords($this->component->getLabel()),
                            '{value}' => MoneyFormatter::formatAsDecimal($this->min, $currencyCode, $locale),
                        ]
                    )
                );
            }
        } catch (ParserException) {
            $fail(
                strtr(
                    'The {attribute} must be a valid numeric value.',
                    [
                        '{attribute}' => ucwords($this->component->getLabel()),
                    ]
                )
            );
        }
    }
}
