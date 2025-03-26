<?php

namespace Pelmered\FilamentMoneyField\Tests\Unit\Casts;

use Money\Currency as MoneyCurrency;
use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\Tests\Support\Models\Post;

beforeEach(function () {
    config(['filament-money-field.currency_cast_to' => Currency::class]);
});

it('casts to currency object', function () {
    $post = Post::factory()->make([
        'price'          => 23523,
        'price_currency' => 'USD',
    ]);

    expect($post->price_currency)
        ->toBeInstanceOf(Currency::class)
        ->and($post->price_currency->getCode())->toBe('USD');
});

it('casts to money currency when configured', function () {
    config(['filament-money-field.currency_cast_to' => MoneyCurrency::class]);

    $model = Post::factory()->make(['price_currency' => 'EUR']);

    expect($model->price_currency)
        ->toBeInstanceOf(MoneyCurrency::class)
        ->and($model->price_currency->getCode())->toBe('EUR');
});

it('handles null values', function () {
    $model = Post::factory()->make([
        'price'          => null,
        'price_currency' => null,
    ]);

    expect($model->price_currency)->toBeNull();
});

it('sets currency from currency instance', function () {
    $model                 = Post::factory()->make();
    $model->price_currency = Currency::fromCode('SEK');

    expect($model->getAttributes()['price_currency'])->toBe('SEK');
});

it('sets currency from money currency instance', function () {
    $model                 = Post::factory()->make();
    $model->price_currency = new MoneyCurrency('GBP');

    expect($model->getAttributes()['price_currency'])->toBe('GBP');
});
