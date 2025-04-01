<?php

use Pelmered\FilamentMoneyField\MoneyFormatter\CurrencyFormattingRules;

it('creates a correct default rules instance', function () {
    $rules = new CurrencyFormattingRules(
        currencySymbol: '$',
        fractionDigits: 2,
        decimalSeparator: '.',
        groupingSeparator: ','
    );

    expect($rules->currencySymbol)->toBe('$')
        ->and($rules->fractionDigits)->toBe(2)
        ->and($rules->decimalSeparator)->toBe('.')
        ->and($rules->groupingSeparator)->toBe(',');
});

it('modifies currency symbol', function () {
    $rules = new CurrencyFormattingRules(
        currencySymbol: '$',
        fractionDigits: 2,
        decimalSeparator: '.',
        groupingSeparator: ','
    );
    $rules->currencySymbol = '€';

    expect($rules->currencySymbol)->toBe('€');
});

it('modifies fraction digits', function () {
    $rules = new CurrencyFormattingRules(
        currencySymbol: '$',
        fractionDigits: 2,
        decimalSeparator: '.',
        groupingSeparator: ','
    );
    $rules->fractionDigits = 4;

    expect($rules->fractionDigits)->toBe(4);
});

it('modifies separators', function () {
    $rules = new CurrencyFormattingRules(
        currencySymbol: '$',
        fractionDigits: 2,
        decimalSeparator: '.',
        groupingSeparator: ','
    );
    $rules->decimalSeparator  = ',';
    $rules->groupingSeparator = ' ';

    expect($rules->decimalSeparator)->toBe(',')
        ->and($rules->groupingSeparator)->toBe(' ');
});
