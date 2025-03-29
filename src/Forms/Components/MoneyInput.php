<?php

namespace Pelmered\FilamentMoneyField\Forms\Components;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Model;
use Money\Money;
use Pelmered\FilamentMoneyField\Concerns\HasMoneyAttributes;
use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\Currencies\CurrencyRepository;
use Pelmered\FilamentMoneyField\Enum\CurrencySymbolPlacement;
use Pelmered\FilamentMoneyField\Forms\Rules\MaxValueRule;
use Pelmered\FilamentMoneyField\Forms\Rules\MinValueRule;
use Pelmered\FilamentMoneyField\MoneyFormatter\MoneyFormatter;
use PhpStaticAnalysis\Attributes\Type;

class MoneyInput extends TextInput
{
    use HasMoneyAttributes;

    protected ?string $symbolPlacement = null;

    /**
     * @var scalar | Closure | null
     */
    protected $maxValue;

    #[Type('scalar | Closure | null')]
    protected $minValue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepare();

        $currencies = CurrencyRepository::getAvailableCurrencies();

        /** @phpstan-ignore if.alwaysTrue */
        if ($this->shouldHaveCurrencySwitcher()) {
            $this->suffixAction(
                Action::make('changeCurrency')
                    ->icon('heroicon-m-arrow-path')
                    ->tooltip('Change currency')
                    ->form([
                        Select::make('currency')
                            ->label('Currency')
                            ->options($currencies->toSelectArray())
                            ->required()
                            ->live(),
                    ])
                    ->action(function (array $data, MoneyInput $component, Model $record, Form $form): void {
                        $money    = $record->{$component->name};
                        $currency = $data['currency'];

                        $record->{$component->name} = new Money(
                            $money->getAmount(),
                            Currency::fromCode($currency)->toMoneyCurrency()
                        );

                        $record->save();
                    })
            );
        }

        $this->formatStateUsing(function (MoneyInput $component, mixed $state): string {

            $this->prepare();

            if (! $state instanceof Money) {
                return '';
            }

            $amount   = $state->getAmount();
            $currency = Currency::fromMoney($state);
            $locale   = $component->getLocale();

            return MoneyFormatter::formatAsDecimal((int) $amount, $currency, $locale, $this->getDecimals());
        });

        $this->dehydrateStateUsing(function (MoneyInput $component, null|int|string $state): ?Money {
            if (! is_numeric($state)) {
                return null;
            }

            $currency = $component->getCurrency();
            $amount   = MoneyFormatter::parseDecimal((string) $state, $currency, $component->getLocale(), $this->getDecimals());

            return new Money((int) $amount, $currency->toMoneyCurrency());
        });
    }

    protected function shouldHaveCurrencySwitcher(): true
    {
        return true;
    }

    protected function prepare(): void
    {
        $this->currencyColumn = $this->name.config('currency_column_suffix', '_currency');
        $symbolPlacement      = $this->getSymbolPlacement();
        $getCurrencySymbol    = function (MoneyInput $component): string {

            /*
            dump(
                $component->name,
                $component->getLocale(),
                $component->getCurrency(),
                MoneyFormatter::getFormattingRules(
                    $component->getLocale(),
                    $component->getCurrency()
                )->currencySymbol
            );
            */
            // ray($component->getLocale(), $component->getCurrency());
            // ray(MoneyFormatter::getFormattingRules($component->getLocale(), $component->getCurrency()));
            return MoneyFormatter::getFormattingRules(
                $component->getLocale(),
                $component->getCurrency()
            )->currencySymbol;
        };

        match ($symbolPlacement) {
            'before' => $this->prefix($getCurrencySymbol)->suffix(null),
            'after'  => $this->suffix($getCurrencySymbol)->prefix(null),
            default  => $this->suffix(null)->prefix(null),
        };

        if (config('filament-money-field.use_input_mask')) {
            $this->mask(function (MoneyInput $component): \Filament\Support\RawJs {
                $formattingRules = MoneyFormatter::getFormattingRules(
                    $component->getLocale(),
                    $component->getCurrency()
                );

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

    public function symbolPlacement(CurrencySymbolPlacement|string $placement): static
    {
        if ($placement instanceof CurrencySymbolPlacement) {
            $placement = $placement->value;
        }

        if (! in_array($placement, CurrencySymbolPlacement::values())) {
            throw new \InvalidArgumentException(
                'Currency symbol placement must be one of: '.implode(', ', CurrencySymbolPlacement::values())
            );
        }

        $this->symbolPlacement = $placement;

        return $this;
    }

    public function hideCurrencySymbol(): static
    {
        return $this->symbolPlacement(CurrencySymbolPlacement::Hidden->value);
    }

    public function minValue(mixed $value): static
    {
        $this->minValue = $value;

        $this->rule(
            static function (MoneyInput $component): MinValueRule {
                return new MinValueRule((int) $component->getMinValue(), $component);
            },
            static fn (MoneyInput $component): bool => filled($component->getMinValue())
        );

        return $this;
    }

    public function maxValue(mixed $value): static
    {
        $this->maxValue = $value;

        $this->rule(
            static function (MoneyInput $component): MaxValueRule {
                return new MaxValueRule((int) $component->getMaxValue(), $component);
            },
            static fn (MoneyInput $component): bool => filled($component->getMaxValue())
        );

        return $this;
    }
}
