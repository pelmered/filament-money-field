<?php

namespace Pelmered\FilamentMoneyField\Forms\Components;

use Closure;
use Filament\Actions\Exports\Concerns\CanFormatState;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
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

        $symbolPlacement = Config::get('filament-money-field.form_currency_symbol_placement', 'before');

        $getCurrencySymbol = function (MoneyInput $component) {
            return MoneyFormatter::getFormattingRules($component->getLocale())->currencySymbol;
        };

        if ($symbolPlacement === 'before') {
            $this->prefix($getCurrencySymbol);
        } else {
            $this->suffix($getCurrencySymbol);
        }

        $this->setUpFormatState();
        $this->setUpDehydrateState();

        if (Config::get('filament-money-field.use_input_mask')) {
            $this->setupInputMask();
        }
    }

    protected function setUpFormatState(): void
    {
        $this->formatStateUsing(function (MoneyInput $component, $state): ?string {
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
    }

    protected function setUpDehydrateState(): void
    {
        $this->dehydrateStateUsing(function (MoneyInput $component, $state): ?string {
            $currency = $component->getCurrency();
            $state    = MoneyFormatter::parseDecimal($state, $currency, $component->getLocale(), $this->getDecimals());

            if (! is_numeric($state)) {
                return null;
            }

            return $state;
        });
    }

    protected function setupInputMask(): void
    {
        $decimals = $this->getDecimals();
        $divisor  = 10 ** $decimals;

        $formattingRules = MoneyFormatter::getFormattingRules($this->getLocale());

        $this->extraAlpineAttributes([
            'x-on:keypress' => 'function() {
                    var charCode = event.keyCode || event.which;
                    /* only number char codes 0-9 */
                    if (charCode < 48 || charCode > 57) {
                        event.preventDefault();
                        return false;
                    }
                    return true;
                }',
            'x-on:keyup' => 'function() {
                    var money = $el.value.replace(/\D/g, "");
                    money = (money / '.$divisor.').toFixed('.$decimals.') + "";
                    money = money.replace("'.$formattingRules->decimalSeparator.'", ",");
                    money = money.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
                    money = money.replace(/(\d)(\d{3}),/g, "$1.$2,");

                    $el.value = money;
                    $el.dispatchEvent(new Event(\'input\'));
                }',
        ]);

        /*
        $this->mask(function (MoneyInput $component) {
            $formattingRules = MoneyFormatter::getFormattingRules($component->getLocale());

            return RawJs::make(
                strtr(
                    '$money($input, \'\', \'{decimalSeparator}\', \'{groupingSeparator}\', {fractionDigits})',
                    [
                        '{decimalSeparator}'  => $formattingRules->decimalSeparator,
                        '{groupingSeparator}' => $formattingRules->groupingSeparator,
                        '{fractionDigits}'    => $formattingRules->fractionDigits,
                    ]
                )
            );

        });
        */
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
        if ($this->label instanceof Htmlable) {
            return $this->label->toHtml();
        }

        return $this->evaluate($this->label)
               ?? (string) str($this->getName())
                   ->afterLast('.')
                   ->kebab()
                   ->replace(['-', '_'], ' ')
                   ->title();
    }
}
