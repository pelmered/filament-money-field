<?php

namespace Pelmered\FilamentMoneyField\Tests;

use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\MoneyFormatter\MoneyFormatter;

function provideMoneyDataSek(): array
{
    return [
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
    ];
}

function provideDecimalMoneyDataSek(): array
{
    return [
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
    ];
}

function provideMoneyDataUsd(): array
{
    return [
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
    ];
}

function provideDecimalMoneyDataUsd(): array
{
    return [
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
    ];
}

function provideDecimalDataSek(): array
{
    return [
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
    ];
}

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

it('formats money in usd', function (mixed $input, string $expectedOutput): void {
    expect(MoneyFormatter::format($input, Currency::fromCode('USD'), 'en_US'))
        ->toBe(replaceNonBreakingSpaces($expectedOutput));
})->with(provideMoneyDataUsd());

it('formats money in sek', function (mixed $input, string $expectedOutput): void {
    $result        = MoneyFormatter::format($input, Currency::fromCode('SEK'), 'sv_SE');
    $cleanResult   = replaceNonBreakingSpaces($result);
    $cleanExpected = replaceNonBreakingSpaces($expectedOutput);

    expect($cleanResult)->toEqual($cleanExpected);
})->with(provideMoneyDataSek());

it('formats decimal money with US locale', function (mixed $input, string $expectedOutput): void {
    expect(MoneyFormatter::numberFormat($input, 'en_US'))
        ->toBe(replaceNonBreakingSpaces($expectedOutput));
})->with(provideDecimalMoneyDataUsd());

it('formats decimal money with Swedish locale', function (mixed $input, string $expectedOutput): void {
    $result        = MoneyFormatter::numberFormat($input, 'sv_SE');
    $cleanResult   = replaceNonBreakingSpaces($result);
    $cleanExpected = replaceNonBreakingSpaces($expectedOutput);

    expect($cleanResult)->toEqual($cleanExpected);
})->with(provideDecimalMoneyDataSek());

it('parses decimal money in sek', function (mixed $input, string $expectedOutput): void {
    expect(MoneyFormatter::parseDecimal($input, Currency::fromCode('SEK'), 'sv_SE'))
        ->toBe($expectedOutput);
})->with(provideDecimalDataSek());

it('parses decimal money in usd', function (mixed $input, string $expectedOutput): void {
    expect(MoneyFormatter::parseDecimal($input, Currency::fromCode('USD'), 'en_US'))
        ->toBe($expectedOutput);
})->with(provideDecimalDataUsd());

it('parses decimal money in usd with intl symbol', function (mixed $input, string $expectedOutput): void {
    config(['filament-money-field.intl_currency_symbol' => true]);

    expect(MoneyFormatter::parseDecimal($input, Currency::fromCode('USD'), 'en_US'))
        ->toBe($expectedOutput);
})->with(provideDecimalDataUsd());

it('parses small decimal money', function (): void {
    expect(MoneyFormatter::parseDecimal('2,00', Currency::fromCode('USD'), 'en_US'))
        ->toBe('20000');
});

it('formats to international currency symbol', function (): void {
    config(['filament-money-field.intl_currency_symbol' => true]);

    $result        = MoneyFormatter::format(100000, Currency::fromCode('USD'), 'en_US');
    $cleanResult   = replaceNonBreakingSpaces($result);
    $cleanExpected = replaceNonBreakingSpaces('USD 1,000.00');

    expect($cleanResult)->toEqual($cleanExpected);
});

it('formats to international currency symbol as suffix', function (): void {
    config(['filament-money-field.intl_currency_symbol' => true]);

    $result        = MoneyFormatter::format(100000, Currency::fromCode('SEK'), 'sv_SE');
    $cleanResult   = replaceNonBreakingSpaces($result);
    $cleanExpected = replaceNonBreakingSpaces('1 000,00 SEK');

    expect($cleanResult)->toEqual($cleanExpected);
});

it('formats with decimal parameter', function (): void {
    expect(MoneyFormatter::format(123456, Currency::fromCode('USD'), 'en_US'))
        ->toBe(replaceNonBreakingSpaces('$1,234.56'))
        ->and(MoneyFormatter::format(123456, Currency::fromCode('USD'), 'en_US', decimals: 0))
        ->toBe(replaceNonBreakingSpaces('$1,235'));
});

it('formats with decimal parameter in sek', function (): void {
    $result        = MoneyFormatter::format(100060, Currency::fromCode('SEK'), 'sv_SE', decimals: 0);
    $cleanResult   = replaceNonBreakingSpaces($result);
    $cleanExpected = replaceNonBreakingSpaces('1 001 kr');

    expect($cleanResult)->toEqual($cleanExpected);
});

it('formats 0 in short format', function (): void {
    expect(MoneyFormatter::formatShort(0, Currency::fromCode('USD'), 'en_US', decimals: -3))
        ->toBe(replaceNonBreakingSpaces('$0'));
});
