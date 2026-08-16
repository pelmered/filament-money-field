<?php

use Money\Currency as MoneyCurrency;
use Money\Money;
use Pelmered\FilamentMoneyField\Casts\CurrencyCast;
use Pelmered\FilamentMoneyField\Casts\MoneyCast;
use Pelmered\FilamentMoneyField\Tests\Support\Models\Post;
use Pelmered\LaraPara\Casts\CurrencyCast as LaraParaCurrencyCast;
use Pelmered\LaraPara\Casts\MoneyCast as LaraParaMoneyCast;
use Pelmered\LaraPara\Currencies\Currency;

// The casts moved to pelmered/larapara, but the README and UPGRADE guide document them under
// this namespace. See https://github.com/pelmered/filament-money-field/issues/99
it('exposes the documented cast classes', function (): void {
    expect(new MoneyCast)->toBeInstanceOf(LaraParaMoneyCast::class)
        ->and(new CurrencyCast)->toBeInstanceOf(LaraParaCurrencyCast::class);
});

it('stores a Money object as minor units plus a currency code', function (): void {
    $post = Post::create([
        'title' => 'Test',
        'price' => new Money(12345, new MoneyCurrency('EUR')),
    ]);

    expect($post->getRawOriginal('price'))->toBe(12345)
        ->and($post->getRawOriginal('price_currency'))->toBe('EUR');
});

it('reads money and currency columns back as value objects', function (): void {
    Post::create([
        'title' => 'Test',
        'price' => new Money(12345, new MoneyCurrency('EUR')),
    ]);

    $post = Post::sole();

    expect($post->price)->toBeInstanceOf(Money::class)
        ->and($post->price->getAmount())->toBe('12345')
        ->and($post->price->getCurrency()->getCode())->toBe('EUR')
        ->and($post->price_currency)->toBeInstanceOf(Currency::class)
        ->and($post->price_currency->getCode())->toBe('EUR');
});
