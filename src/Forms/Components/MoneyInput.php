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

        // $this->container = new ComponentContainer();
        // $this->currencyColumn = 'currency';

        $currencies = CurrencyRepository::getAvailableCurrencies();

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
                        Checkbox::make('convert')
                            ->label('Convert amount to selected currency')
                            ->helperText(function (Get $get) {

                                if (! $get('convert')) {
                                    return null;
                                }

                                $currentCurrency = $this->getCurrency();
                                $newCurrency     = $get('currency');

                                $rate = 1.2234;

                                return 'Conversion rate from '.$currentCurrency->getCode().' to '.$newCurrency.':'.$rate;

                                /*
                                    $exchange = new SwapExchange($swap);

                                    $converter = new Converter(new ISOCurrencies(), $exchange);
                                    $eur100 = Money::EUR(100);
                                    $usd125 = $converter->convert($eur100, new Currency('USD'));
                                    [$usd125, $pair] = $converter->convertAndReturnWithCurrencyPair($eur100, new Currency('USD'));
                                    */
                            })
                            ->default(false),
                    ])
                    ->action(function (array $data, MoneyInput $component, Model $record, Form $form) {
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

        $this->formatStateUsing(function (MoneyInput $component, mixed $state): ?string {

            $this->prepare();

            if (is_null($state) || ! $state instanceof Money) {
                return '';
            }

            $amount   = $state->getAmount();
            $currency = Currency::fromMoney($state) ?? $component->getCurrency();
            $locale   = $component->getLocale();

            return MoneyFormatter::formatAsDecimal((int) $amount, $currency, $locale, $this->getDecimals());
        });

        $this->dehydrateStateUsing(function (MoneyInput $component, null|int|string $state): ?Money {
            /*
            if (! is_numeric($state)) {
                return null;
            }
            */

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
        $getCurrencySymbol    = function (MoneyInput $component) {

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
            $this->mask(function (MoneyInput $component) {
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
            static function (MoneyInput $component) {
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
            static function (MoneyInput $component) {
                return new MaxValueRule((int) $component->getMaxValue(), $component);
            },
            static fn (MoneyInput $component): bool => filled($component->getMaxValue())
        );

        return $this;
    }
}
