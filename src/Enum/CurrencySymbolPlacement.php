<?php

namespace Pelmered\FilamentMoneyField\Enum;

use Pelmered\FilamentMoneyField\Concerns\EnumHelpers;

enum CurrencySymbolPlacement: string
{
    use EnumHelpers;

    case Before = 'before';
    case After  = 'after';
    case Hidden = 'hidden';
}
