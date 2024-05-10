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
            static::replaceNonBreakingSpaces($expectedOutput),
            MoneyFormatter::format($input, new Currency('USD'), 'en_US')
        );
    }

    #[DataProvider('provideMoneyDataSEK')]
    public function testMoneyFormatterSek(mixed $input, string $expectedOutput): void
    {
        self::assertSame(
            static::replaceNonBreakingSpaces($expectedOutput),
            MoneyFormatter::format($input, new Currency('SEK'), 'sv_SE')
        );
    }

    #[DataProvider('provideDecimalMoneyDataUSD')]
    public function testMoneyDecimalFormatterUsd(mixed $input, string $expectedOutput): void
    {
        self::assertSame(
            static::replaceNonBreakingSpaces($expectedOutput),
            MoneyFormatter::formatAsDecimal($input, new Currency('USD'), 'en_US')
        );
    }

    #[DataProvider('provideDecimalMoneyDataSEK')]
    public function testMoneyDecimalFormatterSek(mixed $input, string $expectedOutput): void
    {
        self::assertSame(
            static::replaceNonBreakingSpaces($expectedOutput),
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

    public static function testMoneyParserDecimal(): void
    {
        // Tests for some parsing issues with small numbers such as "2,00" with "," left as thousands separator in the wrong place
        // See: https://github.com/pelmered/filament-money-field/issues/20
        self::assertSame(
            '20000',
            MoneyFormatter::parseDecimal('2,00', new Currency('USD'), 'en_US')
        );
    }
}
