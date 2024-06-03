<?php

namespace Pelmered\FilamentMoneyField\Tests;

use Money\Currency;
use Pelmered\FilamentMoneyField\MoneyFormatter;
use PHPUnit\Framework\Attributes\DataProvider;

final class MoneyFormatterTest extends TestCase
{
    public static function provideMoneyDataSek(): array
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

    public static function provideDecimalMoneyDataSek(): array
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

    public static function provideMoneyDataUsd(): array
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

    public static function provideDecimalMoneyDataUsd(): array
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

    public static function provideDecimalDataSek(): array
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

    public static function provideDecimalDataUsd(): array
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

    #[DataProvider('provideMoneyDataUSD')]
    public function testMoneyFormatterUsd(mixed $input, string $expectedOutput): void
    {
        self::assertSame(
            self::replaceNonBreakingSpaces($expectedOutput),
            MoneyFormatter::format($input, new Currency('USD'), 'en_US')
        );
    }

    #[DataProvider('provideMoneyDataSEK')]
    public function testMoneyFormatterSek(mixed $input, string $expectedOutput): void
    {
        self::assertSame(
            self::replaceNonBreakingSpaces($expectedOutput),
            MoneyFormatter::format($input, new Currency('SEK'), 'sv_SE')
        );
    }

    #[DataProvider('provideDecimalMoneyDataUSD')]
    public function testMoneyDecimalFormatterUsd(mixed $input, string $expectedOutput): void
    {
        self::assertSame(
            self::replaceNonBreakingSpaces($expectedOutput),
            MoneyFormatter::formatAsDecimal($input, new Currency('USD'), 'en_US')
        );
    }

    #[DataProvider('provideDecimalMoneyDataSEK')]
    public function testMoneyDecimalFormatterSek(mixed $input, string $expectedOutput): void
    {
        self::assertSame(
            self::replaceNonBreakingSpaces($expectedOutput),
            MoneyFormatter::formatAsDecimal($input, new Currency('SEK'), 'sv_SE')
        );
    }

    #[DataProvider('provideDecimalDataSEK')]
    public function testMoneyParserDecimalSek(mixed $input, string $expectedOutput): void
    {
        self::assertSame(
            $expectedOutput,
            MoneyFormatter::parseDecimal($input, new Currency('SEK'), 'sv_SE')
        );
    }

    #[DataProvider('provideDecimalDataUSD')]
    public function testMoneyParserDecimalUsd(mixed $input, string $expectedOutput): void
    {
        self::assertSame(
            $expectedOutput,
            MoneyFormatter::parseDecimal($input, new Currency('USD'), 'en_US')
        );
    }

    #[DataProvider('provideDecimalDataUSD')]
    public function testMoneyParserDecimalUsdIntlSymbol(mixed $input, string $expectedOutput): void
    {
        config(['filament-money-field.intl_currency_symbol' => true]);

        self::assertSame(
            $expectedOutput,
            MoneyFormatter::parseDecimal($input, new Currency('USD'), 'en_US')
        );
    }

    public function testMoneyParserDecimal(): void
    {
        // Tests for some parsing issues with small numbers such as "2,00" with "," left as thousands separator in the wrong place
        // See: https://github.com/pelmered/filament-money-field/issues/20
        self::assertSame(
            '20000',
            MoneyFormatter::parseDecimal('2,00', new Currency('USD'), 'en_US')
        );
    }

    public function testInternationalCurrencySymbol(): void
    {
        config(['filament-money-field.intl_currency_symbol' => true]);

        self::assertSame(
            self::replaceNonBreakingSpaces('USD 1,000.00'),
            MoneyFormatter::format(100000, new Currency('USD'), 'en_US')
        );
    }

    public function testInternationalCurrencySymbolSuffix(): void
    {
        config(['filament-money-field.intl_currency_symbol' => true]);

        self::assertSame(
            self::replaceNonBreakingSpaces('1 000,00 SEK'),
            MoneyFormatter::format(100000, new Currency('SEK'), 'sv_SE')
        );
    }

    /*
    public function testGlobalDecimals(): void
    {
        config(['filament-money-field.decimal_digits' => 8]);
        self::assertSame(
            self::replaceNonBreakingSpaces('$1,000.20'),
            MoneyFormatter::format(100020, new Currency('USD'), 'en_US')
        );
        //dd('die', config('filament-money-field.decimal_digits'));

        config(['filament-money-field.decimal_digits' => 0]);
        self::assertSame(
            self::replaceNonBreakingSpaces('$1,000'),
            MoneyFormatter::format(100020, new Currency('USD'), 'en_US')
        );

        config(['filament-money-field.decimal_digits' => 2]);
        self::assertSame(
            self::replaceNonBreakingSpaces('$1,000.11'),
            MoneyFormatter::format(100011, new Currency('USD'), 'en_US')
        );

        config(['filament-money-field.decimal_digits' => 4]);
        self::assertSame(
            self::replaceNonBreakingSpaces('$1,000.7700'),
            MoneyFormatter::format(100077, new Currency('USD'), 'en_US')
        );

        config(['filament-money-field.decimal_digits' => -2]);
        self::assertSame(
            self::replaceNonBreakingSpaces('$120,000'),
            MoneyFormatter::format(12345678, new Currency('USD'), 'en_US')
        );

        config(['filament-money-field.decimal_digits' => -4]);
        self::assertSame(
            self::replaceNonBreakingSpaces('$1,235,000'),
            MoneyFormatter::format(123456789, new Currency('USD'), 'en_US')
        );
    }

    public function testGlobalDecimalsSek(): void
    {
        config(['filament-money-field.decimal_digits' => 0]);
        self::assertSame(
            self::replaceNonBreakingSpaces('1 000 kr'),
            MoneyFormatter::format(100020, new Currency('SEK'), 'sv_SE')
        );

        config(['filament-money-field.decimal_digits' => 2]);
        self::assertSame(
            self::replaceNonBreakingSpaces('1 000,11 kr'),
            MoneyFormatter::format(100011, new Currency('SEK'), 'sv_SE')
        );

        config(['filament-money-field.decimal_digits' => 4]);
        self::assertSame(
            self::replaceNonBreakingSpaces('1 000,7700 kr'),
            MoneyFormatter::format(100077, new Currency('SEK'), 'sv_SE')
        );

        config(['filament-money-field.decimal_digits' => -2]);
        self::assertSame(
            self::replaceNonBreakingSpaces('120 000 kr'),
            MoneyFormatter::format(12345678, new Currency('SEK'), 'sv_SE')
        );

        config(['filament-money-field.decimal_digits' => -4]);
        self::assertSame(
            self::replaceNonBreakingSpaces('1 235 000 kr'),
            MoneyFormatter::format(123456789, new Currency('SEK'), 'sv_SE')
        );
    }
    */

    public function testDecimalsAsParameter(): void
    {
        self::assertSame(
            self::replaceNonBreakingSpaces('$1,234.56'),
            MoneyFormatter::format(123456, new Currency('USD'), 'en_US')
        );
        self::assertSame(
            self::replaceNonBreakingSpaces('$1,235'),
            MoneyFormatter::format(123456, new Currency('USD'), 'en_US', decimals: 0)
        );
        self::assertSame(
            self::replaceNonBreakingSpaces('$1,000.12'),
            MoneyFormatter::format(100012, new Currency('USD'), 'en_US', decimals: 2)
        );
        self::assertSame(
            self::replaceNonBreakingSpaces('$1,000.5500'),
            MoneyFormatter::format(100055, new Currency('USD'), 'en_US', decimals: 4)
        );
        self::assertSame(
            self::replaceNonBreakingSpaces('$1,200'),
            MoneyFormatter::format(123456, new Currency('USD'), 'en_US', decimals: -2)
        );
        self::assertSame(
            self::replaceNonBreakingSpaces('$123,500'),
            MoneyFormatter::format(12345678, new Currency('USD'), 'en_US', decimals: -4)
        );
    }

    public function testDecimalsAsParameterSek(): void
    {
        self::assertSame(
            self::replaceNonBreakingSpaces('1 001 kr'),
            MoneyFormatter::format(100060, new Currency('SEK'), 'sv_SE', decimals: 0)
        );
        self::assertSame(
            self::replaceNonBreakingSpaces('1 000,12 kr'),
            MoneyFormatter::format(100012, new Currency('SEK'), 'sv_SE', decimals: 2)
        );
        self::assertSame(
            self::replaceNonBreakingSpaces('1 000,5500 kr'),
            MoneyFormatter::format(100055, new Currency('SEK'), 'sv_SE', decimals: 4)
        );
        self::assertSame(
            self::replaceNonBreakingSpaces('1 200 kr'),
            MoneyFormatter::format(123456, new Currency('SEK'), 'sv_SE', decimals: -2)
        );
        self::assertSame(
            self::replaceNonBreakingSpaces('123 500 kr'),
            MoneyFormatter::format(12345678, new Currency('SEK'), 'sv_SE', decimals: -4)
        );
    }
}
