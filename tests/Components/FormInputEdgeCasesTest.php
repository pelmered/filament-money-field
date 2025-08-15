<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Get as F3Get;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Money\Currency;
use Money\Money;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Helper;
use Pelmered\FilamentMoneyField\Tests\Support\Components\TestComponent;

it('handles empty string properly', function (): void {

    $component = createFormTestComponent(
        [MoneyInput::make('price')],
        [],
        'price',
    );

    $component = getComponent($component, 'price');

    // Using an empty string should result in a null value
    $moneyValue = $component->getState('');

    expect($moneyValue)->toBeNull();
});

it('handles extremely large amounts properly', function (): void {
    // Use actual Money object directly since filling with string isn't working
    $largeMoneyValue = new Money('999999999999', new Currency('USD'));

    $component = createFormTestComponent(
        [MoneyInput::make('money')],
        ['money' => $largeMoneyValue],
        'money',
    );

    $state = $component->getState();
    expect($state)->toBeArray();
    expect($state['money'])->toBeInstanceOf(Money::class);
    expect($state['money']->getAmount())->toBe('999999999999');
});

it('handles currency changes gracefully', function (): void {
    // First test with USD currency
    $component1 = createFormTestComponent(
        [MoneyInput::make('money')],
        ['money' => new Money('12345', new Currency('USD'))],
        'money',
    );

    $state = $component1->getState();
    expect($state)->toBeArray();
    expect($state['money'])->toBeInstanceOf(Money::class);
    expect($state['money']->getAmount())->toBe('12345');
    expect($state['money']->getCurrency()->getCode())->toBe('USD');

    // Create a new component with EUR currency but use the same Money object with USD
    $component2 = createFormTestComponent(
        [MoneyInput::make('money')->currency('EUR')],
        ['money' => new Money('123456', new Currency('USD'))],
        'money',
    );

    $state = $component2->getState();
    expect($state)->toBeArray();
    expect($state['money'])->toBeInstanceOf(Money::class);
    expect($state['money']->getAmount())->toBe('123456');
    // The currency should match what we provided in the fill
    expect($state['money']->getCurrency()->getCode())->toBe('EUR');
});

it('handles negative values correctly', function (): void {
    $component = createFormTestComponent(
        [MoneyInput::make('price')],
        ['price' => new Money('-50000', new Currency('USD'))],
        'price',
    );

    expect($component->getState()['price']->getAmount())->toBe('-50000');
});

it('handles zero values correctly', function (): void {
    $component = createFormTestComponent(
        [MoneyInput::make('price')],
        ['price' => new Money('0', new Currency('USD'))],
        'price',
    );

    expect($component->getState()['price']->getAmount())->toBe('0');
});

it('handles empty string as null', function (): void {
    $component = createFormTestComponent(
        [MoneyInput::make('price')],
        ['price' => ''],
        'price',
    );

    expect($component->getState()['price'])->toBeNull();
});

it('handles custom step values', function (): void {
    $component = createFormTestComponent(
        [MoneyInput::make('price')->step(0.01)],
        ['price' => new Money('10001', new Currency('USD'))],
        'price',
    );

    $field = getComponent($component,'price');

    expect($field->getStep())->toBe(0.01);
});

it('handles hidden state correctly', function (): void {
    if (Helper::isFilament3())
    {
        $moneyInput = MoneyInput::make('price')->hidden(fn (F3Get $get): bool => (bool) $get('hide_price'));
    }
    else
    {
        $moneyInput = MoneyInput::make('price')->hidden(fn (Get $get): bool => (bool) $get('hide_price'));
    }

    $component = createFormTestComponent(
        [$moneyInput],
        [
            'price'      => new Money('10000', new Currency('USD')),
            'hide_price' => false,
        ],
        'price',
    );


    $field = getComponent($component,'price');
    expect($field->isHidden())->toBeFalse();

    $component->fill([
        'price'      => new Money('10000', new Currency('USD')),
        'hide_price' => true,
    ]);

    expect($field->isHidden())->toBeTrue();
});
