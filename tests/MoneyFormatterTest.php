<?php

namespace Pelmered\FilamentMoneyField\Tests;
use Money\Currency;
use Pelmered\FilamentMoneyField\MoneyFormatter;
use PHPUnit\Framework;

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


    /**
     * @covers MoneyFormatter::format
     * @dataProvider provideMoneyDataSEK
     */
    #[Framework\CoversClass(MoneyFormatter::class)]
    public function testMoneyFormatterSEK(mixed $input, string $expectedOutput)
    {
        self::assertSame(
            static::replaceNonBreakingSpaces($expectedOutput),
            MoneyFormatter::format($input, new Currency('SEK'), 'sv_SE')
        );
    }

    /**
     * @covers MoneyFormatter::format
     * @dataProvider provideMoneyDataUSD
     */
    #[Framework\CoversClass(MoneyFormatter::class)]
    public function testMoneyFormatterUSD(mixed $input, string $expectedOutput)
    {
        self::assertSame(
            static::replaceNonBreakingSpaces($expectedOutput),
            MoneyFormatter::format($input, new Currency('USD'), 'en_US')
        );
    }

    /**
     * @covers MoneyFormatter::parseDecimal
     * @dataProvider provideDecimalDataSEK
     */
    #[Framework\CoversClass(MoneyFormatter::class)]
    public function testMoneyParserDecimalSEK(mixed $input, string $expectedOutput)
    {
        self::assertSame(
            $expectedOutput,
            MoneyFormatter::parseDecimal($input, new Currency('SEK'), 'sv_SE')
        );
    }

    /**
     * @covers MoneyFormatter::parseDecimal
     * @dataProvider provideDecimalDataUSD
     */
    #[Framework\CoversClass(MoneyFormatter::class)]
    public function testMoneyParserDecimalUSD(mixed $input, string $expectedOutput)
    {
        self::assertSame(
            $expectedOutput,
            MoneyFormatter::parseDecimal($input, new Currency('USD'), 'en_US')
        );
    }
}
