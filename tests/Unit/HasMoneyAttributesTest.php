<?php

use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;
use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;
use Pelmered\FilamentMoneyField\Tests\TestCase;

it('uses the configured currency column suffix', function (string $component): void {
    config(['larapara.currency_column_suffix' => '_iso']);

    expect(TestCase::callMethod($component::make('price'), 'getCurrencyColumn', []))->toBe('price_iso');
})->with([
    'input'  => [MoneyInput::class],
    'column' => [MoneyColumn::class],
    'entry'  => [MoneyEntry::class],
]);

it('falls back to the app locale when no default locale is configured', function (?string $locale): void {
    config([
        'filament-money-field.default_locale' => $locale,
        'app.locale'                          => 'sv_SE',
    ]);

    expect(MoneyInput::make('price')->getLocale())->toBe('sv_SE');
})->with([
    'null'         => [null],
    'empty string' => [''],
]);
