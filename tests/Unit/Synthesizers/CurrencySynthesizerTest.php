<?php

namespace Pelmered\FilamentMoneyField\Tests\Unit\Synthesizers;

use Livewire\Mechanisms\HandleComponents\ComponentContext;
use Mockery;
use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\Synthesizers\CurrencySynthesizer;

afterEach(function () {
    Mockery::close();
});

it('can synthesize currencies', function () {
    $context  = Mockery::mock(ComponentContext::class);
    $currency = Currency::fromCode('USD');

    $synthesizer = new CurrencySynthesizer($context, 'price');

    expect($synthesizer::match($currency))->toBeTrue();
    expect($synthesizer::match(new \stdClass))->toBeFalse();

    $dehydrated = $synthesizer->dehydrate($currency);

    expect($dehydrated)->toBeArray();
    expect($dehydrated)->toHaveCount(2);
    expect($dehydrated[0])->toBe('USD');
    expect($dehydrated[1])->toBe([]);

    $hydrated = $synthesizer->hydrate('USD');

    /*
    if ($hydrated instanceof Currency::class) {
        $this->fail('Expected hydrate to return a Currency instance.');
    }
    */

    expect($hydrated)->toBeInstanceOf(Currency::class);
    expect($hydrated->getCode())->toBe('USD');
});
