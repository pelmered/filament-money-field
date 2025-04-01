<?php

namespace Pelmered\FilamentMoneyField\Tests\Unit\Synthesizers;

use Livewire\Mechanisms\HandleComponents\ComponentContext;
use Mockery;
use Money\Money;
use Pelmered\FilamentMoneyField\Synthesizers\MoneySynthesizer;

afterEach(function (): void {
    Mockery::close();
});


it('can synthesize money', function (): void {
    // Create a mocked context
    $context = Mockery::mock(ComponentContext::class);

    $currency = \Pelmered\FilamentMoneyField\Currencies\Currency::fromCode('USD');
    $money    = new Money(123456, $currency->toMoneyCurrency());

    $synthesizer = new MoneySynthesizer($context, 'price');

    expect($synthesizer::match($money))->toBeTrue();
    expect($synthesizer::match(new \stdClass))->toBeFalse();

    $dehydrated = $synthesizer->dehydrate($money);

    expect($dehydrated)->toBeArray();
    expect($dehydrated)->toHaveCount(2);
    expect($dehydrated[0])->toHaveCount(2);
    expect($dehydrated[1])->toBe([]);

    expect($dehydrated[0]['amount'])->toBe('123456');
    expect($dehydrated[0]['currency']->getCode())->toBe('USD');

    $hydrated = $synthesizer->hydrate(['amount' => 2412, 'currency' => 'USD']);

    /*
    if ($hydrated instanceof Money::class) {
        $this->fail('Expected hydrate to return a Money instance.');
    }
    */

    expect($hydrated)->toBeInstanceOf(Money::class);
    expect($hydrated->getAmount())->toBe('2412');
    expect($hydrated->getCurrency()->getCode())->toBe('USD');
});
