<?php

namespace Pelmered\FilamentMoneyField\Tests\Unit\Synthesizers;

use Filament\Forms\ComponentContainer;
use Livewire\Mechanisms\HandleComponents\ComponentContext;
use Mockery;
use Mockery\MockInterface;
use Money\Money;
use Nette\Schema\Expect;
use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\Exceptions\UnsupportedCurrency;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Synthesizers\CurrencySynthesizer;
use Pelmered\FilamentMoneyField\Tests\Support\Components\FormTestComponent;

afterEach(function () {
    Mockery::close();
});

it('can synthesize currencies', function () {
    $component = ComponentContainer::make(FormTestComponent::make())
                                   ->statePath('data')
                                   ->components([
                                       MoneyInput::make('price')->currencySwitcherEnabled()
                                   ])
                                   ->fill(['price' => new Money(123456, new \Money\Currency('EUR'))]);

    $context ??= new ComponentContext($component);
    $currency = Currency::fromCode('USD');

    $synthesizer = new CurrencySynthesizer($context, 'price');

    expect($synthesizer::match($currency))->toBeTrue();
    expect($synthesizer::match(new \stdClass()))->toBeFalse();

    $dehydrated = $synthesizer->dehydrate($currency);

    expect($dehydrated)->toBeArray();
    expect($dehydrated)->toHaveCount(2);
    expect($dehydrated[0])->toBe('USD');
    expect($dehydrated[1])->toBe([]);

    $hydrated = $synthesizer->hydrate('USD');

    if ($hydrated instanceof Currency::class) {
        $this->fail('Expected hydrate to return a Currency instance.');
    }

    expect($hydrated)->toBeInstanceOf(Currency::class);
    expect($hydrated->getCode())->toBe('USD');
});

