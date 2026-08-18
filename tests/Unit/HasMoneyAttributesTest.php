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

it('falls back to the app locale when no default locale is configured', function (): void {
    config([
        'filament-money-field.default_locale' => null,
        'app.locale'                          => 'sv_SE',
    ]);

    expect(MoneyInput::make('price')->getLocale())->toBe('sv_SE');
});

it('takes the minor units default from the store format', function (?string $format, bool $inMinor): void {
    config(['larapara.store.format' => $format]);

    expect(TestCase::callMethod(MoneyInput::make('price'), 'getInMinorUnits', []))->toBe($inMinor);
})->with([
    'int'     => ['int', true],
    'decimal' => ['decimal', false],
    'not set' => [null, true],
]);
