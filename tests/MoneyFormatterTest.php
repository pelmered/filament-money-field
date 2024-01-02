<?php declare(strict_types=1);

namespace Pelmered\FilamentMoneyField\Tests;
use Money\Currency;
use Pelmered\FilamentMoneyField\MoneyFormatter;

final class MoneyFormatterTest extends TestCase
{
    public static function provideMoneyDataSEK(): array
    {
        return [
            'thousands' => [
                '10 000,00 kr',
                1000000,
            ],
            'decimals' => [
                '100,45 kr',
                10045,
            ],
            'millions' => [
                '1 234 567,89 kr',
                123456789,
            ],
        ];
    }

    public static function provideMoneyDataUSD(): array
    {
        return [
            'thousands' => [
                '$10,000.00',
                1000000,
            ],
            'decimals' => [
                '$100.45',
                10045,
            ],
            'millions' => [
                '$1,234,567.89',
                123456789,
            ],
        ];
    }


    /**
     * @dataProvider provideMoneyDataSEK
     */
    public function testMoneyFormatterSEK( string $expectedOutput, mixed $input)
    {
        self::assertSame(
            static::replaceNonBreakingSpaces($expectedOutput),
            MoneyFormatter::format($input, new Currency('SEK'), 'sv_SE')
        );
    }

    /**
     * @dataProvider provideMoneyDataUSD
     */
    public function testMoneyFormatterUSD( string $expectedOutput, mixed $input)
    {
        self::assertSame(
            static::replaceNonBreakingSpaces($expectedOutput),
            MoneyFormatter::format($input, new Currency('USD'), 'en_US')
        );
    }



}
