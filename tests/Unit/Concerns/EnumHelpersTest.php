<?php

namespace Pelmered\FilamentMoneyField\Tests\Unit\Concerns;

use Pelmered\FilamentMoneyField\Tests\TestCase;
use Pelmered\FilamentMoneyField\Tests\Unit\Concerns\Support\TestEnum;

it('can get all enum names using names() method', function () {
    expect(TestEnum::names())->toBe(['Red', 'Green', 'Blue'])
        ->and(TestEnum::names())->toHaveCount(3);
});

it('can get all enum values using values() method', function () {
    expect(TestEnum::values())->toBe(['red', 'green', 'blue'])
        ->and(TestEnum::values())->toHaveCount(3);
});

it('can get array with value => name pairs using array() method', function () {
    expect(TestEnum::array())->toBe([
        'red' => 'Red',
        'green' => 'Green',
        'blue' => 'Blue',
    ]);
}); 