<?php

use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Forms\Rules\MaxValueRule;
use Pelmered\FilamentMoneyField\Forms\Rules\MinValueRule;
use Pelmered\FilamentMoneyField\Tests\TestCase;

uses(TestCase::class);

it('validates min value', function () {
    $rule = new MinValueRule(10000, new MoneyInput('totalAmount'));

    $rule->validate('totalAmount', 16, function ($message) {
        expect($message)->toEqual('The Total Amount must be at least 100.00.');
    });

    $rule->validate('amount', 'invalid', function ($message) {
        expect($message)->toEqual('The Amount must be a valid numeric value.');
    });
});

it('validates max value', function () {
    $rule = new MaxValueRule(10000, new MoneyInput('amount'));

    $rule->validate('totalAmount', 30000, function ($message) {
        expect($message)->toEqual('The Total Amount must be less than or equal to 100.00.');
    });

    $rule->validate('amount', 'invalid', function ($message) {
        expect($message)->toEqual('The Amount must be a valid numeric value.');
    });
});
