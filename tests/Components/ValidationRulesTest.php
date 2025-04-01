<?php

use Filament\Forms\ComponentContainer;
use Illuminate\Support\Facades\Validator;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Forms\Rules\MaxValueRule;
use Pelmered\FilamentMoneyField\Forms\Rules\MinValueRule;
use Pelmered\FilamentMoneyField\Tests\Support\Components\FormTestComponent;

it('validates min value using MinValueRule', function (): void {
    // Create the input component for testing
    $moneyInput = MoneyInput::make('money')
        ->currency('USD')
        ->locale('en_US');

    // Initialize the component
    $container = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([$moneyInput]);

    $fieldComponent = $container->getComponent('data.money');

    // Test valid value
    $rule      = new MinValueRule(1000, $fieldComponent);
    $validator = Validator::make(
        ['money' => '15.00'],
        [
            'money' => function ($attribute, $value, $fail) use ($rule): void {
                $rule->validate($attribute, $value, $fail);
            },
        ]
    );
    expect($validator->passes())->toBeTrue();

    // Test invalid value
    $rule      = new MinValueRule(2000, $fieldComponent);
    $validator = Validator::make(
        ['money' => '15.00'],
        [
            'money' => function ($attribute, $value, $fail) use ($rule): void {
                $rule->validate($attribute, $value, $fail);
            },
        ]
    );
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('money'))->toContain('least');
});

it('validates max value using MaxValueRule', function (): void {
    // Create the input component for testing
    $moneyInput = MoneyInput::make('money')
        ->currency('USD')
        ->locale('en_US');

    // Initialize the component
    $container = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([$moneyInput]);

    $fieldComponent = $container->getComponent('data.money');

    // Test valid value
    $rule      = new MaxValueRule(2000, $fieldComponent);
    $validator = Validator::make(
        ['money' => '15.00'],
        [
            'money' => function ($attribute, $value, $fail) use ($rule): void {
                $rule->validate($attribute, $value, $fail);
            },
        ]
    );
    expect($validator->passes())->toBeTrue();

    // Test invalid value
    $rule      = new MaxValueRule(1000, $fieldComponent);
    $validator = Validator::make(
        ['money' => '15.00'],
        [
            'money' => function ($attribute, $value, $fail) use ($rule): void {
                $rule->validate($attribute, $value, $fail);
            },
        ]
    );
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('money'))->toContain('must be less than');
});

it('can validate required money array format', function (): void {
    $validator = Validator::make([
        'field' => null,
    ], [
        'field'          => ['required', 'array'],
        'field.amount'   => ['required', 'numeric'],
        'field.currency' => ['required', 'string'],
    ]);

    expect($validator->passes())->toBeFalse();

    $validator = Validator::make([
        'field' => [
            'amount'   => 1000,
            'currency' => 'USD',
        ],
    ], [
        'field'          => ['required', 'array'],
        'field.amount'   => ['required', 'numeric'],
        'field.currency' => ['required', 'string'],
    ]);

    expect($validator->passes())->toBeTrue();
});

it('can validate money array with missing values', function (): void {
    $validator = Validator::make([
        'field' => [
            'amount' => 1000,
        ],
    ], [
        'field'          => ['required', 'array'],
        'field.amount'   => ['required', 'numeric'],
        'field.currency' => ['required', 'string'],
    ]);

    expect($validator->passes())->toBeFalse();

    $validator = Validator::make([
        'field' => [
            'currency' => 'USD',
        ],
    ], [
        'field'          => ['required', 'array'],
        'field.amount'   => ['required', 'numeric'],
        'field.currency' => ['required', 'string'],
    ]);

    expect($validator->passes())->toBeFalse();
});
