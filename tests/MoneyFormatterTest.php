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

    self::assertSame(
        replaceNonBreakingSpaces('1 000,00 SEK'),
        MoneyFormatter::format(100000, new Currency('SEK'), 'sv_SE')
    );
});

it('formats with decimal parameter', function () {
    self::assertSame(
        replaceNonBreakingSpaces('$1,234.56'),
        MoneyFormatter::format(123456, new Currency('USD'), 'en_US')
    );
    self::assertSame(
        replaceNonBreakingSpaces('$1,235'),
        MoneyFormatter::format(123456, new Currency('USD'), 'en_US', decimals: 0)
    );
    self::assertSame(
        replaceNonBreakingSpaces('$1,000.12'),
        MoneyFormatter::format(100012, new Currency('USD'), 'en_US', decimals: 2)
    );
    self::assertSame(
        replaceNonBreakingSpaces('$1,000.5500'),
        MoneyFormatter::format(100055, new Currency('USD'), 'en_US', decimals: 4)
    );
    self::assertSame(
        replaceNonBreakingSpaces('$1,200'),
        MoneyFormatter::format(123456, new Currency('USD'), 'en_US', decimals: -2)
    );
    self::assertSame(
        replaceNonBreakingSpaces('$123,500'),
        MoneyFormatter::format(12345678, new Currency('USD'), 'en_US', decimals: -4)
    );
});

it('formats with decimal parameter in sek', function () {
    self::assertSame(
        replaceNonBreakingSpaces('1 001 kr'),
        MoneyFormatter::format(100060, new Currency('SEK'), 'sv_SE', decimals: 0)
    );
    self::assertSame(
        replaceNonBreakingSpaces('1 000,12 kr'),
        MoneyFormatter::format(100012, new Currency('SEK'), 'sv_SE', decimals: 2)
    );
    self::assertSame(
        replaceNonBreakingSpaces('1 000,5500 kr'),
        MoneyFormatter::format(100055, new Currency('SEK'), 'sv_SE', decimals: 4)
    );
    self::assertSame(
        replaceNonBreakingSpaces('1 200 kr'),
        MoneyFormatter::format(123456, new Currency('SEK'), 'sv_SE', decimals: -2)
    );
    self::assertSame(
        replaceNonBreakingSpaces('123 500 kr'),
        MoneyFormatter::format(12345678, new Currency('SEK'), 'sv_SE', decimals: -4)
    );
});
