<?php

use Filament\Forms\ComponentContainer;
use Illuminate\Support\Facades\Validator;
use Money\Currency;
use Money\Money;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Forms\Rules\MaxValueRule;
use Pelmered\FilamentMoneyField\Forms\Rules\MinValueRule;
use Pelmered\FilamentMoneyField\Tests\Support\Components\FormTestComponent;
use Pelmered\FilamentMoneyField\Tests\TestCase;

uses(TestCase::class);

it('validates min value', function ($data, $minValue, bool $expected, $errors = null) {
    // Extract field key
    $fieldKey = array_key_first($data);

    // Convert Money object to string value if needed
    $inputValue = $data[$fieldKey];
    if ($inputValue instanceof Money) {
        $inputValue = (string) ($inputValue->getAmount() / 100); // Convert to decimal string
    }
    $validationData = [$fieldKey => $inputValue];

    // Create the MoneyInput
    $moneyInput = new MoneyInput($fieldKey);

    // Initialize the component in a container
    $container = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([$moneyInput])
        ->fill([$moneyInput->getName() => $data[$fieldKey]]);

    // Get the initialized component from the container
    $initializedComponent = $container->getComponent('data.'.$moneyInput->getName());

    // Create ValidationRule
    $rule = new MinValueRule($minValue, $initializedComponent);

    // Create custom validator that will call the validate method directly
    $validator = Validator::make(
        $validationData,
        [$fieldKey => function ($attribute, $value, $fail) use ($rule) {
            $rule->validate($attribute, $value, $fail);
        }]
    );

    expect($validator->passes())->toBe($expected);

    if ($errors) {
        expect($validator->errors()->toArray())->toEqual($errors);
    }
})->with([
    'same value' => [
        ['total' => new Money(100, new Currency('USD'))],
        10000,
        false,
    ],
    'higher value' => [
        ['amount' => 200],
        11000,
        true,
    ],
    'lower value' => [
        ['total' => new Money(100, new Currency('USD'))],
        15000,
        false,
        ['total' => ['The Total must be at least 150.00.']],
    ],
    'invalid value' => [
        ['totalAmount' => 'invalid'],
        10000,
        false,
        ['totalAmount' => ['The Total Amount must be a valid numeric value.']],
    ],
]);

it('validates max value', function ($data, $maxValue, bool $expected, $errors = null) {
    // Extract field key
    $fieldKey = array_key_first($data);

    // Convert Money object to string value if needed
    $inputValue = $data[$fieldKey];
    if ($inputValue instanceof Money) {
        $inputValue = (string) ($inputValue->getAmount() / 100); // Convert to decimal string
    }
    $validationData = [$fieldKey => $inputValue];

    // Create the MoneyInput
    $moneyInput = new MoneyInput($fieldKey);

    // Initialize the component in a container
    $container = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([$moneyInput])
        ->fill([$moneyInput->getName() => $data[$fieldKey]]);

    // Get the initialized component from the container
    $initializedComponent = $container->getComponent('data.'.$moneyInput->getName());

    // Create ValidationRule
    $rule = new MaxValueRule($maxValue, $initializedComponent);

    // Create custom validator that will call the validate method directly
    $validator = Validator::make(
        $validationData,
        [$fieldKey => function ($attribute, $value, $fail) use ($rule) {
            $rule->validate($attribute, $value, $fail);
        }]
    );

    expect($validator->passes())->toBe($expected);

    if ($errors) {
        expect($validator->errors()->toArray())->toEqual($errors);
    }
})->with([
    'same value' => [
        ['total' => new Money(100, new Currency('USD'))],
        10000,
        true,
    ],
    'higher value' => [
        ['amount' => 200],
        11000,
        false,
        ['amount' => ['The Amount must be less than or equal to 110.00.']],
    ],
    'lower value' => [
        ['total' => new Money(90, new Currency('USD'))],
        9900,
        true,
    ],
    'invalid value' => [
        ['totalAmount' => 'invalid'],
        10000,
        false,
        ['totalAmount' => ['The Total Amount must be a valid numeric value.']],
    ],
]);
