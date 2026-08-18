<?php

declare(strict_types=1);

use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Tests\Support\Components\FormTestComponent;
use Pelmered\FilamentMoneyField\Tests\Support\Models\Post;
use Pelmered\LaraPara\Exceptions\UnsupportedCurrency;

function fieldOnRecord(MoneyInput $input, Model $record): MoneyInput
{
    Schema::make(FormTestComponent::make())
        ->model($record)
        ->statePath('data')
        ->components([$input])
        ->fill();

    return $input;
}

it('prefers a currency set on the field over the record', function (): void {
    $field = fieldOnRecord(
        MoneyInput::make('price')->currency('EUR'),
        new Post(['title' => 'Test', 'price_currency' => 'SEK']),
    );

    expect($field->getCurrency()->getCode())->toBe('EUR');
});

it("reads the currency from the record's currency column", function (): void {
    $field = fieldOnRecord(
        MoneyInput::make('price'),
        new Post(['title' => 'Test', 'price_currency' => 'SEK']),
    );

    expect($field->getCurrency()->getCode())->toBe('SEK');
});

it('falls back to the default currency when the record has no currency yet', function (mixed $emptyCurrency): void {
    $field = fieldOnRecord(
        MoneyInput::make('price'),
        new Post(['title' => 'Test', 'price' => null, 'price_currency' => $emptyCurrency]),
    );

    expect($field->getCurrency()->getCode())->toBe('USD');
})->with([
    'null' => [null],
    'empty string' => [''],
]);

it('falls back to the default currency when the record has no currency column', function (): void {
    $field = fieldOnRecord(
        MoneyInput::make('title'),
        new Post(['title' => 'No companion currency column exists for this one']),
    );

    expect($field->getCurrency()->getCode())->toBe('USD');
});

it('still rejects a currency column holding an unknown code', function (): void {
    expect(fn (): string => fieldOnRecord(
        MoneyInput::make('price'),
        new Post(['title' => 'Test', 'price_currency' => 'NOPE']),
    )->getCurrency()->getCode())->toThrow(UnsupportedCurrency::class);
});
