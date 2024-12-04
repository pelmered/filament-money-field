<?php

namespace Pelmered\FilamentMoneyField\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Pelmered\FilamentMoneyField\Concerns\HasMoneyAttributes;
use Pelmered\FilamentMoneyField\Forms\Rules\MaxValueRule;
use Pelmered\FilamentMoneyField\Forms\Rules\MinValueRule;
use Pelmered\FilamentMoneyField\MoneyFormatter\MoneyFormatter;
use Closure;

class MoneyInput extends TextInput
{
    use HasMoneyAttributes;

    protected ?string $symbolPlacement = null;

    /**
     * @var scalar | Closure | null
     */
    protected $maxValue = null;

    /**
     * @var scalar | Closure | null
     */
    protected $minValue = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepare();

        $this->formatStateUsing(function (MoneyInput $component, $state): ?string {

            $this->prepare();

            $currency = $component->getCurrency();
            $locale   = $component->getLocale();

            if (is_null($state)) {
                return null;
            }
            if (! is_numeric($state)) {
                return $state;
            }

            return MoneyFormatter::formatAsDecimal((int) $state, $currency, $locale, $this->getDecimals());
        });

        $this->dehydrateStateUsing(function (MoneyInput $component, $state): ?string {
            $currency = $component->getCurrency();
            $state    = MoneyFormatter::parseDecimal($state, $currency, $component->getLocale(), $this->getDecimals());

            if (! is_numeric($state)) {
                return null;
            }

            return $state;
        });
    }

    protected function prepare(): void
    {
        $symbolPlacement   = $this->getSymbolPlacement();
        $getCurrencySymbol = function (MoneyInput $component) {
            return MoneyFormatter::getFormattingRules($component->getLocale(), $component->getCurrency())->currencySymbol;
        };

        match ($symbolPlacement) {
            'before' => $this->prefix($getCurrencySymbol)->suffix(null),
            'after'  => $this->suffix($getCurrencySymbol)->prefix(null),
            default  => $this->suffix(null)->prefix(null),
        };

        if (config('filament-money-field.use_input_mask')) {
            $this->mask(function (MoneyInput $component) {
                $formattingRules = MoneyFormatter::getFormattingRules($component->getLocale(), $component->getCurrency());

                return RawJs::make(
                    strtr(
                        '$money($input, \'{decimalSeparator}\', \'{groupingSeparator}\', {fractionDigits})',
                        [
                            '{decimalSeparator}'  => $formattingRules->decimalSeparator,
                            '{groupingSeparator}' => $formattingRules->groupingSeparator,
                            '{fractionDigits}'    => $formattingRules->fractionDigits,
                        ]
                    )
                );
            });
        }
    }

    public function getSymbolPlacement(): string
    {
        return $this->symbolPlacement ?? config('filament-money-field.form_currency_symbol_placement', 'before');
    }

    public function symbolPlacement(string|\Closure|null $symbolPlacement = null): static
    {
        $this->symbolPlacement = $this->evaluate($symbolPlacement);

        if (! in_array($this->symbolPlacement, ['before', 'after', 'hidden'])) {
            throw new \InvalidArgumentException('Symbol placement must be either "before", "after" or "hidden".');
        }

        return $this;
    }

    public function minValue(mixed $value): static
    {
        $this->minValue = $value;

        $this->rule(
            static function (MoneyInput $component) {
                $value = $component->getMinValue();
                return new MinValueRule($value, $component);
            },
            static fn (MoneyInput $component): bool => filled($component->getMinValue())
        );

        return $this;
    }

    public function maxValue(mixed $value): static
    {
        $this->maxValue = $value;

        $this->rule(
            static function (MoneyInput $component) {
                $value = $component->getMaxValue();
                return new MaxValueRule($value, $component);
            },
            static fn (MoneyInput $component): bool => filled($component->getMaxValue())
        );

        return $this;
    }
}
