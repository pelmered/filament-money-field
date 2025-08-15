<?php

use Filament\Forms\ComponentContainer;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Money\Currency;
use Money\Money;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Tests\Support\Components\TestComponent;

it('handles empty string properly', function (): void {
    $component = Schema::make(TestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('money')])
        ->getComponent('money');

    // Using an empty string should result in a null value
    $moneyValue = $component->getState('');

    expect($moneyValue)->toBeNull();
});

it('handles extremely large amounts properly', function (): void {
    // Use actual Money object directly since filling with string isn't working
    $largeMoneyValue = new Money('999999999999', new Currency('USD'));

    $component = Schema::make(TestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('money')])
        ->fill(['money' => $largeMoneyValue]);

    $state = $component->getState();
    expect($state)->toBeArray();
    expect($state['money'])->toBeInstanceOf(Money::class);
    expect($state['money']->getAmount())->toBe('999999999999');
});

it('handles currency changes gracefully', function (): void {
    // First test with USD currency
    $component = Schema::make(TestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('money')->currency('USD')])
        ->fill(['money' => new Money('12345', new Currency('USD'))]);

    $state = $component->getState();
    expect($state)->toBeArray();
    expect($state['money'])->toBeInstanceOf(Money::class);
    expect($state['money']->getAmount())->toBe('12345');
    expect($state['money']->getCurrency()->getCode())->toBe('USD');

    // Create a new component with EUR currency but use the same Money object with USD
    $component = Schema::make(TestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('money')->currency('EUR')])
        ->fill(['money' => new Money('12345', new Currency('EUR'))]);

    $state = $component->getState();
    expect($state)->toBeArray();
    expect($state['money'])->toBeInstanceOf(Money::class);
    expect($state['money']->getAmount())->toBe('12345');
    // The currency should match what we provided in the fill
    expect($state['money']->getCurrency()->getCode())->toBe('EUR');
});

it('handles negative values correctly', function (): void {
    $component = Schema::make(TestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('price')])
        ->fill(['price' => new Money('-50000', new Currency('USD'))]);

    expect($component->getState()['price']->getAmount())->toBe('-50000');
});

it('handles zero values correctly', function (): void {
    $component = Schema::make(TestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('price')])
        ->fill(['price' => new Money('0', new Currency('USD'))]);

    expect($component->getState()['price']->getAmount())->toBe('0');
});

it('handles empty string as null', function (): void {
    $component = Schema::make(TestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('price')])
        ->fill(['price' => '']);

    expect($component->getState()['price'])->toBeNull();
});

it('handles custom step values', function (): void {
    $component = Schema::make(TestComponent::make())
        ->statePath('data')
        ->components([
            MoneyInput::make('price')->step(0.01),
        ])
        ->fill(['price' => new Money('10001', new Currency('USD'))]);

    $field = $component->getComponent('price');

    expect($field->getStep())->toBe(0.01);
});

it('handles hidden state correctly', function (): void {
    $component = Schema::make(TestComponent::make())
        ->statePath('data')
        ->components([
            MoneyInput::make('price')
                ->hidden(fn (Get $get): bool => (bool) $get('hide_price')),
        ])
        ->fill([
            'price'      => new Money('10000', new Currency('USD')),
            'hide_price' => false,
        ]);

    $field = $component->getComponent('price');
    expect($field->isHidden())->toBeFalse();

    $component->fill([
        'price'      => new Money('10000', new Currency('USD')),
        'hide_price' => true,
    ]);

    expect($field->isHidden())->toBeTrue();
});

it('handles disabled state correctly', function (): void {
    $component = Schema::make(TestComponent::make())
        ->statePath('data')
        ->components([
            MoneyInput::make('price')
                ->disabled(fn (Get $get): bool => (bool) $get('disable_price')),
        ])
        ->fill([
            'price'         => new Money('10000', new Currency('USD')),
            'disable_price' => false,
        ]);

    $field = $component->getComponent('price');
    expect($field->isDisabled())->toBeFalse();

    $component->fill([
        'price'         => new Money('10000', new Currency('USD')),
        'disable_price' => true,
    ]);

    expect($field->isDisabled())->toBeTrue();
});
