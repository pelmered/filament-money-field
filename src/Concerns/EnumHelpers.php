<?php

namespace Pelmered\FilamentMoneyField\Concerns;

use PhpStaticAnalysis\Attributes\Returns;

trait EnumHelpers
{
    #[Returns('array<string>')]
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    #[Returns('array<string>')]
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    #[Returns('array<string, string>')]
    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }
}
