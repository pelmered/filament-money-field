<?php

namespace Pelmered\FilamentMoneyField\Tests\Unit\Synthesizers;

use Filament\Forms\ComponentContainer;
use Livewire\Mechanisms\HandleComponents\ComponentContext;
use Mockery;
use Mockery\MockInterface;
use Money\Currency;
use Money\Money;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Synthesizers\CurrencySynthesizer;
use Pelmered\FilamentMoneyField\Synthesizers\MoneySynthesizer;
use Pelmered\FilamentMoneyField\Tests\Support\Components\FormTestComponent;

afterEach(function () {
    Mockery::close();
});


it('can synthesize money', function () {
    $component = ComponentContainer::make(FormTestComponent::make())
                                   ->statePath('data')
                                   ->components([
                                       MoneyInput::make('price')->currencySwitcherEnabled()
                                   ])
                                   ->fill(['price' => new Money(123456, new \Money\Currency('EUR'))]);

    $context ??= new ComponentContext($component);
    $currency = \Pelmered\FilamentMoneyField\Currencies\Currency::fromCode('USD');
    $money = new Money(123456, $currency->toMoneyCurrency());

    $synthesizer = new MoneySynthesizer($context, 'price');

    expect($synthesizer::match($money))->toBeTrue();
    expect($synthesizer::match(new \stdClass()))->toBeFalse();

    $dehydrated = $synthesizer->dehydrate($money);

    expect($dehydrated)->toBeArray();
    expect($dehydrated)->toHaveCount(2);
    expect($dehydrated[0])->toHaveCount(2);
    expect($dehydrated[1])->toBe([]);

    expect($dehydrated[0]['amount'])->toBe('123456');
    expect($dehydrated[0]['currency']->getCode())->toBe('USD');

    $hydrated = $synthesizer->hydrate(['amount' => 2412, 'currency' => 'USD']);

    if ($hydrated instanceof Money::class) {
        $this->fail('Expected hydrate to return a Money instance.');
    }

    expect($hydrated)->toBeInstanceOf(Money::class);
    expect($hydrated->getAmount())->toBe('2412');
    expect($hydrated->getCurrency()->getCode())->toBe('USD');
});
