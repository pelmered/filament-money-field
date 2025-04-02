<?php

namespace Pelmered\FilamentMoneyField\Tests\Unit\Enum;

use Pelmered\FilamentMoneyField\Enum\CurrencySymbolPlacement;
use Pelmered\FilamentMoneyField\Tests\TestCase;

it('has correct cases defined', function () {
    expect(CurrencySymbolPlacement::cases())->toHaveCount(3)
        ->and(CurrencySymbolPlacement::Before->value)->toBe('before')
        ->and(CurrencySymbolPlacement::After->value)->toBe('after')
        ->and(CurrencySymbolPlacement::Hidden->value)->toBe('hidden');
});

it('can get all enum names', function () {
    expect(CurrencySymbolPlacement::names())->toBe(['Before', 'After', 'Hidden'])
        ->and(CurrencySymbolPlacement::names())->toHaveCount(3);
});

it('can get all enum values', function () {
    expect(CurrencySymbolPlacement::values())->toBe(['before', 'after', 'hidden'])
        ->and(CurrencySymbolPlacement::values())->toHaveCount(3);
});

it('can get array with value => name pairs', function () {
    expect(CurrencySymbolPlacement::array())->toBe([
        'before' => 'Before',
        'after' => 'After',
        'hidden' => 'Hidden',
    ]);
}); 