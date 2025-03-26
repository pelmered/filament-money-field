<?php

use Illuminate\Support\Facades\Validator;
use Money\Currency;
use Money\Money;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Forms\Rules\MaxValueRule;
use Pelmered\FilamentMoneyField\Forms\Rules\MinValueRule;
use Pelmered\FilamentMoneyField\Tests\TestCase;

uses(TestCase::class);

it('validates min value', function ($data, $rules, bool $expected, $errors = null) {

    $validator = Validator::make(
        $data,
        $rules,
    );

    expect($validator->passes())->toBe($expected);

    if ($errors) {
        expect($validator->errors()->toArray())->toEqual($errors);
    }

})->with([
    'same value' => [
        ['total' => new Money(100, new Currency('USD'))],
        ['total' => new MinValueRule(10000, new MoneyInput('total'))],
        true,
    ],
    'higher value' => [
        ['amount' => 200],
        ['amount' => new MinValueRule(11000, new MoneyInput('amount'))],
        true,
    ],
    'lower value' => [
        ['total' => new Money(100, new Currency('USD'))],
        ['total' => new MinValueRule(15000, new MoneyInput('total'))],
        false,
        ['total' => ['The Total must be at least 150.00.']],
    ],
    'invalid value' => [
        ['totalAmount' => 'invalid'],
        ['totalAmount' => new MinValueRule(10000, new MoneyInput('totalAmount'))],
        false,
        ['totalAmount' => ['The Total Amount must be a valid numeric value.']],
    ],
]);

it('validates max value', function ($data, $rules, bool $expected, $errors = null) {

    $validator = Validator::make(
        $data,
        $rules,
    );

    expect($validator->passes())->toBe($expected);

    if ($errors) {
        expect($validator->errors()->toArray())->toEqual($errors);
    }

})->with([
    'same value' => [
        ['total' => new Money(100, new Currency('USD'))],
        ['total' => new MaxValueRule(10000, new MoneyInput('total'))],
        true,
    ],
    'higher value' => [
        ['amount' => 200],
        ['amount' => new MaxValueRule(11000, new MoneyInput('amount'))],
        false,
        ['amount' => ['The Amount must be less than or equal to 110.00.']],
    ],
    'lower value' => [
        ['total' => new Money(90, new Currency('USD'))],
        ['total' => new MaxValueRule(9900, new MoneyInput('total'))],
        true,
    ],
    'invalid value' => [
        ['totalAmount' => 'invalid'],
        ['totalAmount' => new MaxValueRule(10000, new MoneyInput('totalAmount'))],
        false,
        ['totalAmount' => ['The Total Amount must be a valid numeric value.']],
    ],
]);
