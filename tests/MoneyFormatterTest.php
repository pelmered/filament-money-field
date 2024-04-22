<?php

namespace Pelmered\FilamentMoneyField\Tests;
use Money\Currency;
use Pelmered\FilamentMoneyField\MoneyFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(MoneyFormatter::class)]
final class MoneyFormatterTest extends TestCase
{
    public static function provideMoneyDataSEK(): array
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

    public static function provideDecimalMoneyDataSEK(): array
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

    public static function provideMoneyDataUSD(): array
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

    public static function provideDecimalMoneyDataUSD(): array
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

    public static function provideDecimalDataSEK(): array
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

    public static function provideDecimalDataUSD(): array
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
    public function testMoneyFormatterUSD(mixed $input, string $expectedOutput)
    {
        self::assertSame(
            static::replaceNonBreakingSpaces($expectedOutput),
            MoneyFormatter::format($input, new Currency('USD'), 'en_US')
        );
    }

    #[DataProvider('provideMoneyDataSEK')]
    public function testMoneyFormatterSEK(mixed $input, string $expectedOutput)
    {
        self::assertSame(
            static::replaceNonBreakingSpaces($expectedOutput),
            MoneyFormatter::format($input, new Currency('SEK'), 'sv_SE')
        );
    }

    #[DataProvider('provideDecimalMoneyDataUSD')]
    //#[CoversClass(MoneyFormatter::class)]
    public function testMoneyDecimalFormatterUSD(mixed $input, string $expectedOutput)
    {
        self::assertSame(
            static::replaceNonBreakingSpaces($expectedOutput),
            MoneyFormatter::formatAsDecimal($input, new Currency('USD'), 'en_US')
        );
    }

    #[DataProvider('provideDecimalMoneyDataSEK')]
    //#[CoversClass(MoneyFormatter::class)]
    public function testMoneyDecimalFormatterSEK(mixed $input, string $expectedOutput)
    {
        self::assertSame(
            static::replaceNonBreakingSpaces($expectedOutput),
            MoneyFormatter::formatAsDecimal($input, new Currency('SEK'), 'sv_SE')
        );
    }

    #[DataProvider('provideDecimalDataSEK')]
    //#[CoversClass(MoneyFormatter::class)]
    public function testMoneyParserDecimalSEK(mixed $input, string $expectedOutput)
    {
        self::assertSame(
            $expectedOutput,
            MoneyFormatter::parseDecimal($input, new Currency('SEK'), 'sv_SE')
        );
    }

    #[DataProvider('provideDecimalDataUSD')]
    public function testMoneyParserDecimalUSD(mixed $input, string $expectedOutput)
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
