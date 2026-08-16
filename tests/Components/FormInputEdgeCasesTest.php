<?php

use Filament\Schemas\Components\Utilities\Get;
use Money\Currency;
use Money\Money;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;

it('handles empty string properly', function (): void {

    $component = createFormTestComponent(
        [MoneyInput::make('price')],
        ['price' => ''],
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

    $state = getComponentState($component, 'money');

    expect($state)->toEqual('9,999,999,999.99');
});

it('handles currency changes gracefully', function (): void {
    // First test with USD currency
    $component1 = createFormTestComponent(
        [MoneyInput::make('money')],
        ['money' => new Money('12345', new Currency('USD'))],
        'money',
    );

    $state = getComponentState($component1, 'money');
    expect($state)->toBe('123.45');

    // Create a new component with EUR currency but use the same Money object with USD
    $component2 = createFormTestComponent(
        [MoneyInput::make('money')->currency('EUR')],
        ['money' => new Money('123456', new Currency('USD'))],
        'money',
    );

    $state = getComponentState($component2, 'money');
    expect($state)->toBe('1,234.56');
});

it('handles negative values correctly', function (): void {
    $component = createFormTestComponent(
        [MoneyInput::make('price')],
        ['price' => new Money('-50000', new Currency('USD'))],
        'price',
    );

    expect(getComponentState($component, 'price'))->toBe('-500.00');
});

it('handles zero values correctly', function (): void {
    $component = createFormTestComponent(
        [MoneyInput::make('price')],
        ['price' => new Money('0', new Currency('USD'))],
        'price',
    );

    $state = getComponentState($component, 'price');
    expect($state)->toBe('0.00');
});

it('handles empty string as null', function (): void {
    $component = createFormTestComponent(
        [MoneyInput::make('price')],
        ['price' => ''],
        'price',
    );

    expect(getComponentState($component, 'price'))->toBeNull();
});

it('handles custom step values', function (): void {
    $component = createFormTestComponent(
        [MoneyInput::make('price')->step(0.01)],
        ['price' => new Money('10001', new Currency('USD'))],
        'price',
    );

    $field = getComponent($component, 'price');

    expect($field->getStep())->toBe(0.01);
});

it('handles hidden state correctly', function (): void {
    $moneyInput = MoneyInput::make('price')->hidden(fn (Get $get): bool => (bool) $get('hide_price'));

    $component = createFormTestComponent(
        [$moneyInput],
        [
            'price'      => new Money('10000', new Currency('USD')),
            'hide_price' => false,
        ],
        'price',
    );

    $field = getComponent($component, 'price');
    expect($field->isHidden())->toBeFalse();

    $component->fill([
        'price'      => new Money('10000', new Currency('USD')),
        'hide_price' => true,
    ]);

    expect($field->isHidden())->toBeTrue();
});
