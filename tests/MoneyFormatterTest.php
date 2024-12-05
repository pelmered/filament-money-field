<?php

namespace Pelmered\FilamentMoneyField\Tests;

use Money\Currency;
use Pelmered\FilamentMoneyField\MoneyFormatter;

uses(TestCase::class);

function provideDecimalDataUsd(): array
{
    return [
        'thousands' => [
            '10,000.00',
            '1000000',
        ],
        'decimals' => [
            '100.45',
            '10045',
        ],
        'millions' => [
            '1,234,567.89',
            '123456789',
        ],
        'empty_string' => [
            '',
            '',
        ],
        'null' => [
            null,
            '',
        ],
    ];
}

it('formats money in usd', function (mixed $input, string $expectedOutput) {
    expect(MoneyFormatter::format($input, new Currency('USD'), 'en_US'))
        ->toBe(replaceNonBreakingSpaces($expectedOutput));
})->with([
    'thousands' => [
        1000000,
        '$10,000.00',
    ],
    'decimals' => [
        10045,
        '$100.45',
    ],
    'millions' => [
        123456789,
        '$1,234,567.89',
    ],
    'empty_string' => [
        '',
        '',
    ],
    'null' => [
        null,
        '',
    ],
]);

it('formats money in sek', function (mixed $input, string $expectedOutput) {
    expect(MoneyFormatter::format($input, new Currency('SEK'), 'sv_SE'))
        ->toBe(replaceNonBreakingSpaces($expectedOutput));
})->with([
    'thousands' => [
        1000000,
        '10 000,00 kr',
    ],
    'decimals' => [
        10045,
        '100,45 kr',
    ],
    'millions' => [
        123456789,
        '1 234 567,89 kr',
    ],
    'empty_string' => [
        '',
        '',
    ],
    'null' => [
        null,
        '',
    ],
]);

it('formats decimal money in usd', function (mixed $input, string $expectedOutput) {
    expect(MoneyFormatter::formatAsDecimal($input, new Currency('USD'), 'en_US'))
        ->toBe(replaceNonBreakingSpaces($expectedOutput));
})->with([
    'thousands' => [
        1000000,
        '10,000.00',
    ],
    'decimals' => [
        10045,
        '100.45',
    ],
    'millions' => [
        123456789,
        '1,234,567.89',
    ],
    'empty_string' => [
        '',
        '',
    ],
    'null' => [
        null,
        '',
    ],
]);

it('formats decimal money in sek', function (mixed $input, string $expectedOutput) {
    expect(MoneyFormatter::formatAsDecimal($input, new Currency('SEK'), 'sv_SE'))
        ->toBe(replaceNonBreakingSpaces($expectedOutput));
})->with([
    'thousands' => [
        1000000,
        '10 000,00',
    ],
    'decimals' => [
        10045,
        '100,45',
    ],
    'millions' => [
        123456789,
        '1 234 567,89',
    ],
    'empty_string' => [
        '',
        '',
    ],
    'null' => [
        null,
        '',
    ],
]);

it('parses decimal money in sek', function (mixed $input, string $expectedOutput) {
    expect(MoneyFormatter::parseDecimal($input, new Currency('SEK'), 'sv_SE'))
        ->toBe(replaceNonBreakingSpaces($expectedOutput));
})->with([
    'thousands' => [
        '10 000,00',
        '1000000',
    ],
    'decimals' => [
        '100,45',
        '10045',
    ],
    'millions' => [
        '1 234 567,89',
        '123456789',
    ],
    'empty_string' => [
        '',
        '',
    ],
    'null' => [
        null,
        '',
    ],
]);

it('parses decimal money in usd', function (mixed $input, string $expectedOutput) {
    expect(MoneyFormatter::parseDecimal($input, new Currency('USD'), 'en_US'))
        ->toBe(replaceNonBreakingSpaces($expectedOutput));
})->with(provideDecimalDataUSD());

it('parses decimal money in usd with intl symbol', function (mixed $input, string $expectedOutput) {
    config(['filament-money-field.intl_currency_symbol' => true]);

    expect(MoneyFormatter::parseDecimal($input, new Currency('USD'), 'en_US'))
        ->toBe(replaceNonBreakingSpaces($expectedOutput));
})->with(provideDecimalDataUSD());

it('parses small decimal money', function () {
    // Tests for some parsing issues with small numbers such as "2,00" with "," left as thousands separator in the wrong place
    // See: https://github.com/pelmered/filament-money-field/issues/20
    expect(MoneyFormatter::parseDecimal('2,00', new Currency('USD'), 'en_US'))
        ->toBe('20000');
});

it('formats to international currency symbol', function () {
    config(['filament-money-field.intl_currency_symbol' => true]);

    expect(MoneyFormatter::format(100000, new Currency('USD'), 'en_US'))
        ->toBe(replaceNonBreakingSpaces('USD 1,000.00'));
});

it('formats tointernational currency symbol as suffix', function () {
    config(['filament-money-field.intl_currency_symbol' => true]);

    expect(MoneyFormatter::format(100000, new Currency('SEK'), 'sv_SE'))
        ->toBe(replaceNonBreakingSpaces('1 000,00 SEK'));
});

it('formats with decimal parameter', function ($amount, $decimals, $expected) {
    expect(MoneyFormatter::format($amount, new Currency('USD'), 'en_US', decimals: $decimals))
        ->toBe(replaceNonBreakingSpaces($expected));
})->with([
    [123456, 2, '$1,234.56'],
    [123456, 0, '$1,235'],
    [100012, 2, '$1,000.12'],
    [100055, 4, '$1,000.5500'],
    [123456, -2, '$1,200'],
    [12345678, -4, '$123,500'],
]);

it('formats with decimal parameter in sek', function ($amount, $decimals, $expected) {

    expect(MoneyFormatter::format($amount, new Currency('SEK'), 'sv_SE', decimals: $decimals))
        ->toBe(replaceNonBreakingSpaces($expected));

})->with([
    [100060, 0, '1 001 kr'],
    [100012, 2, '1 000,12 kr'],
    [100055, 4, '1 000,5500 kr'],
    [123456, -2, '1 200 kr'],
    [12345678, -4, '123 500 kr'],
]);

it('formats to short format', function (mixed $input, string $expectedOutput) {
    expect(MoneyFormatter::formatShort($input, new Currency('USD'), 'en_US'))
        ->toBe(replaceNonBreakingSpaces($expectedOutput));
})->with([
    'invalid' => [
        'invalid',
        '',
    ],
    'small 1' => [
        123,
        '$1.23',
    ],
    'small 2' => [
        12300,
        '$123.00',
    ],
    'thousands' => [
        123456,
        '$1.23K',
    ],
    'millions' => [
        1234567890,
        '$12.35M',
    ],
    'billions' => [
        100000000,
        '$1.00M',
    ],
]);

it('formats to short format with decimals', function (mixed $input, int $decimals, string $expectedOutput) {
    expect(MoneyFormatter::formatShort($input, new Currency('USD'), 'en_US', precision: $decimals))
        ->toBe(replaceNonBreakingSpaces($expectedOutput));
})->with([
    'thousands with 0 decimals' => [
        123456,
        0,
        '$1K',
    ],
    'thousands with 2 decimals' => [
        123456,
        2,
        '$1.23K',
    ],
    'thousands with 4 decimals' => [
        123456,
        4,
        '$1.2346K',
    ],
    'thousands with -2 decimals' => [
        123456,
        -2,
        '$1.2K',
    ],
    'thousands with -4 decimals' => [
        123456,
        -4,
        '$1.235K',
    ],
    'millions' => [
        1234567890,
        2,
        '$12.35M',
    ],
    'billions' => [
        100000000,
        2,
        '$1.00M',
    ],
]);

it('formats to short format with SEK', function (mixed $input, string $expectedOutput) {
    expect(MoneyFormatter::formatShort($input, new Currency('SEK'), 'sv_SE'))
        ->toBe(replaceNonBreakingSpaces($expectedOutput));
})->with([
    'thousands' => [
        123456,
        '1,23K kr',
    ],
    'millions' => [
        1234567890,
        '12,35M kr',
    ],
    'billions' => [
        100100000,
        '1,00M kr',
    ],
]);

it('formats to short format with USD and hidden currency symbol', function (mixed $input, string $expectedOutput) {
    expect(MoneyFormatter::formatShort($input, new Currency('USD'), 'en_US', showCurrencySymbol: false))
        ->toBe(replaceNonBreakingSpaces($expectedOutput));
})->with([
    'thousands' => [
        123456,
        '1.23K',
    ],
    'millions' => [
        1234567890,
        '12.35M',
    ],
    'billions' => [
        100000000,
        '1.00M',
    ],
]);

it('formats to short format with SEK and hidden currency symbol', function (mixed $input, string $expectedOutput) {
    expect(MoneyFormatter::formatShort($input, new Currency('SEK'), 'sv_SE', showCurrencySymbol: false))
        ->toBe(replaceNonBreakingSpaces($expectedOutput));
})->with([
    'thousands' => [
        123456,
        '1,23K',
    ],
    'millions' => [
        1234567890,
        '12,35M',
    ],
    'billions' => [
        100000000,
        '1,00M',
    ],
]);


