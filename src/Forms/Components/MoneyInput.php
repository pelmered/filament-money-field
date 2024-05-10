<?php

namespace Pelmered\FilamentMoneyField\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Config;
use Pelmered\FilamentMoneyField\Forms\Rules\MaxValueRule;
use Pelmered\FilamentMoneyField\Forms\Rules\MinValueRule;
use Pelmered\FilamentMoneyField\HasMoneyAttributes;
use Pelmered\FilamentMoneyField\MoneyFormatter;

class MoneyInput extends TextInput
{
    use HasMoneyAttributes;


    protected function setUp(): void
    {
        parent::setUp();

        $this->prepare();

        $this->formatStateUsing(function (MoneyInput $component, $state): ?string {

            $this->prepare();

            $currency = $component->getCurrency();
            $locale = $component->getLocale();

            if (is_null($state)) {
                return null;
            }
            if (!is_numeric($state)) {
                return $state;
            }

            return MoneyFormatter::formatAsDecimal((int) $state, $currency, $locale);
        });

        $this->dehydrateStateUsing(function (MoneyInput $component, $state): ?string {
            $currency = $component->getCurrency();
            $state = MoneyFormatter::parseDecimal($state, $currency, $component->getLocale());

            if (!is_numeric($state)) {
                return null;
            }

            return $state;
        });
    }

    protected function prepare(): void
    {
        $symbolPlacement = Config::get('filament-money-field.form_currency_symbol_placement', 'before');

        $getCurrencySymbol = function (MoneyInput $component) {
            return MoneyFormatter::getFormattingRules($component->getLocale())->currencySymbol;
        };

        if ($symbolPlacement === 'before') {
            $this->prefix($getCurrencySymbol);
        } else {
            $this->suffix($getCurrencySymbol);
        }

        if (config('filament-money-field.use_input_mask')) {
            $this->mask(function (MoneyInput $component) {
                $formattingRules = MoneyFormatter::getFormattingRules($component->getLocale());

                return RawJs::make(
                    strtr(
                        '$money($input, \'\', \'{decimalSeparator}\', \'{groupingSeparator}\', {fractionDigits})',
                        [
                            '{decimalSeparator}' => $formattingRules->decimalSeparator,
                            '{groupingSeparator}' => $formattingRules->groupingSeparator,
                            '{fractionDigits}' => $formattingRules->fractionDigits,
                        ]
                    )
                );
            });
        }
    }

    public function minValue(mixed $value): static
    {
        $this->rule(new MinValueRule((int) $this->evaluate($value), $this));
        return $this;
    }

    public function maxValue(mixed $value): static
    {
        $this->rule(new MaxValueRule((int) $this->evaluate($value), $this));
        return $this;
    }


    public function getLabel(): string
    {
        return $this->evaluate($this->label)
               ?? (string)str($this->getName())
                ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->title();
    }
}
