<?php

namespace Pelmered\FilamentMoneyField\Tests\Unit\Concerns\Support;

use Pelmered\FilamentMoneyField\Concerns\EnumHelpers;

enum TestEnum: string
{
    use EnumHelpers;

    case Red = 'red';
    case Green = 'green';
    case Blue = 'blue';
} 