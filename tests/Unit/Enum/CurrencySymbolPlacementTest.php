<?php

namespace Pelmered\FilamentMoneyField\Tests\Unit\Enum;

use Pelmered\FilamentMoneyField\Enum\CurrencySymbolPlacement;

it('has correct cases defined', function (): void {
    expect(CurrencySymbolPlacement::cases())->toHaveCount(3)
        ->and(CurrencySymbolPlacement::Before->value)->toBe('before')
        ->and(CurrencySymbolPlacement::After->value)->toBe('after')
        ->and(CurrencySymbolPlacement::Hidden->value)->toBe('hidden');
});

it('can get all enum names', function (): void {
    expect(CurrencySymbolPlacement::names())->toBe(['Before', 'After', 'Hidden'])
        ->and(CurrencySymbolPlacement::names())->toHaveCount(3);
});

it('can get all enum values', function (): void {
    expect(CurrencySymbolPlacement::values())->toBe(['before', 'after', 'hidden'])
        ->and(CurrencySymbolPlacement::values())->toHaveCount(3);
});

it('can get array with value => name pairs', function (): void {
    expect(CurrencySymbolPlacement::array())->toBe([
        'before' => 'Before',
        'after'  => 'After',
        'hidden' => 'Hidden',
    ]);
});
