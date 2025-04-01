<?php

use Illuminate\Support\Facades\Validator;
use Pelmered\FilamentMoneyField\Forms\Rules\MoneyCurrency;
use Pelmered\FilamentMoneyField\Forms\Rules\MoneyMax;
use Pelmered\FilamentMoneyField\Forms\Rules\MoneyMin;

it('validates currency correctly', function () {
    $rule = new MoneyCurrency(['USD', 'EUR']);

    $validator = Validator::make(['amount' => ['amount' => 1000, 'currency' => 'USD']], [
        'amount' => [$rule],
    ]);

    expect($validator->passes())->toBeTrue();

    $validator = Validator::make(['amount' => ['amount' => 1000, 'currency' => 'SEK']], [
        'amount' => [$rule],
    ]);

    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('amount'))->toContain('The selected currency is invalid');
});

it('validates minimum money amount correctly', function () {
    $rule = new MoneyMin(1000);

    // Test valid amount
    $validator = Validator::make(['amount' => ['amount' => 1500, 'currency' => 'USD']], [
        'amount' => [$rule],
    ]);

    expect($validator->passes())->toBeTrue();

    // Test invalid amount
    $validator = Validator::make(['amount' => ['amount' => 500, 'currency' => 'USD']], [
        'amount' => [$rule],
    ]);

    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('amount'))->toContain('must be at least');
});

it('validates maximum money amount correctly', function () {
    $rule = new MoneyMax(1000);

    // Test valid amount
    $validator = Validator::make(['amount' => ['amount' => 500, 'currency' => 'USD']], [
        'amount' => [$rule],
    ]);

    expect($validator->passes())->toBeTrue();

    // Test invalid amount
    $validator = Validator::make(['amount' => ['amount' => 1500, 'currency' => 'USD']], [
        'amount' => [$rule],
    ]);

    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('amount'))->toContain('may not be greater than');
});

it('validates minimum for negative amounts', function () {
    $rule = new MoneyMin(-500);

    // Test valid negative amount (greater than min)
    $validator = Validator::make(['amount' => ['amount' => -200, 'currency' => 'USD']], [
        'amount' => [$rule],
    ]);

    expect($validator->passes())->toBeTrue();

    // Test invalid negative amount (less than min)
    $validator = Validator::make(['amount' => ['amount' => -700, 'currency' => 'USD']], [
        'amount' => [$rule],
    ]);

    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('amount'))->toContain('must be at least');
});

it('validates maximum for negative amounts', function () {
    $rule = new MoneyMax(-500);

    // Test valid negative amount (less than max)
    $validator = Validator::make(['amount' => ['amount' => -700, 'currency' => 'USD']], [
        'amount' => [$rule],
    ]);

    expect($validator->passes())->toBeTrue();

    // Test invalid negative amount (greater than max)
    $validator = Validator::make(['amount' => ['amount' => -200, 'currency' => 'USD']], [
        'amount' => [$rule],
    ]);

    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('amount'))->toContain('may not be greater than');
});

it('properly formats currency values in error messages', function () {
    $rule                        = new MoneyMin(1000);
    $rule->formatNumberInMessage = true;

    // Test with USD
    $validator = Validator::make(['amount' => ['amount' => 500, 'currency' => 'USD']], [
        'amount' => [$rule],
    ]);

    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('amount'))->toContain('$10.00');

    // Test with EUR
    $validator = Validator::make(['amount' => ['amount' => 500, 'currency' => 'EUR']], [
        'amount' => [$rule],
    ]);

    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('amount'))->toContain('€10.00');
});
