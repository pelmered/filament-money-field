<?php

use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\Currencies\CurrencyCollection;

it('can create a new currency collection', function (): void {
    $collection = new CurrencyCollection;

    expect($collection)->toBeInstanceOf(CurrencyCollection::class);
});

it('can transform currencies to select array', function (): void {
    // Create a collection with sample currency objects
    $collection = new CurrencyCollection([
        'USD' => new Currency('USD', 'US Dollar', 2),
        'EUR' => new Currency('EUR', 'Euro', 2),
        'SEK' => new Currency('SEK', 'Swedish Krona', 2),
    ]);

    $selectArray = $collection->toSelectArray();

    // Check that the transformed array has the expected structure
    expect($selectArray)
        ->toBeArray()
        ->toHaveCount(3)
        ->toHaveKeys(['USD', 'EUR', 'SEK'])
        ->and($selectArray['USD'])->toBe('US Dollar')
        ->and($selectArray['EUR'])->toBe('Euro')
        ->and($selectArray['SEK'])->toBe('Swedish Krona');
});

it('returns empty array when collection is empty', function (): void {
    $collection = new CurrencyCollection;

    $selectArray = $collection->toSelectArray();

    expect($selectArray)
        ->toBeArray()
        ->toBeEmpty();
});

it('maintains collection functionality', function (): void {
    // Test that the class properly extends Laravel Collection
    $collection = new CurrencyCollection([
        'USD' => new Currency('USD', 'US Dollar', 2),
        'EUR' => new Currency('EUR', 'Euro', 2),
    ]);

    // Test a few collection methods to ensure inheritance works correctly
    expect($collection->count())->toBe(2)
        ->and($collection->has('USD'))->toBeTrue()
        ->and($collection->has('JPY'))->toBeFalse()
        ->and($collection->keys()->toArray())->toBe(['USD', 'EUR']);

    // Test adding a new currency
    $collection->put('JPY', new Currency('JPY', 'Japanese Yen', 0));
    expect($collection->count())->toBe(3)
        ->and($collection->has('JPY'))->toBeTrue();
});
