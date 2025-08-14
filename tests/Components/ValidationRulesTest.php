<?php

use Illuminate\Support\Facades\Validator;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Forms\Rules\MaxValueRule;
use Pelmered\FilamentMoneyField\Forms\Rules\MinValueRule;
use Pelmered\FilamentMoneyField\Tests\Support\Components\FormTestComponent;
use Filament\Schemas\Components\Component;

it('validates min value using MinValueRule', function (): void {
    // Create the input component for testing
    $moneyInput = MoneyInput::make('money')
        ->currency('USD')
        ->locale('en_US');

    // Test valid value
    $rule      = new MinValueRule(1000, $moneyInput);
    $validator = Validator::make(
        ['money' => '15.00'],
        [
            'money' => function ($attribute, $value, $fail) use ($rule): void {
                $rule->validate($attribute, $value, $fail);
            },
        ]
    );
    expect($validator->passes())->toBeTrue();

    // Test too low value
    $rule      = new MinValueRule(2000, $moneyInput);
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

    // Test invalid(non-numeric) value
    $rule      = new MinValueRule(1000, $moneyInput);
    $validator = Validator::make(
        ['money' => 'abc'],
        [
            'money' => function ($attribute, $value, $fail) use ($rule): void {
                $rule->validate($attribute, $value, $fail);
            },
        ]
    );
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('money'))->toContain('must be a valid numeric value');
});

it('validates max value using MaxValueRule', function (): void {
    // Create the input component for testing
    $moneyInput = MoneyInput::make('money')
        ->currency('USD')
        ->locale('en_US');

    // Test valid value
    $rule      = new MaxValueRule(2000, $moneyInput);
    $validator = Validator::make(
        ['money' => '15.00'],
        [
            'money' => function ($attribute, $value, $fail) use ($rule): void {
                $rule->validate($attribute, $value, $fail);
            },
        ]
    );
    expect($validator->passes())->toBeTrue();

    // Test too high value
    $rule      = new MaxValueRule(1000, $moneyInput);
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

    // Test invalid(non-numeric) value
    $rule      = new MaxValueRule(1000, $moneyInput);
    $validator = Validator::make(
        ['money' => 'abc'],
        [
            'money' => function ($attribute, $value, $fail) use ($rule): void {
                $rule->validate($attribute, $value, $fail);
            },
        ]
    );
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('money'))->toContain('must be a valid numeric value');
});
