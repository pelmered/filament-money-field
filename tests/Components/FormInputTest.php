<?php

use Filament\Forms\ComponentContainer;
use Illuminate\Validation\ValidationException;
use Money\Currency;
use Money\Exception\ParserException;
use Money\Money;
use Pelmered\FilamentMoneyField\Exceptions\UnsupportedCurrency;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Forms\Rules\MaxValueRule;
use Pelmered\FilamentMoneyField\Forms\Rules\MinValueRule;
use Pelmered\FilamentMoneyField\Tests\Support\Components\FormTestComponent;
use Pelmered\FilamentMoneyField\Tests\TestCase;

uses(TestCase::class);

it('accepts form input money in numeric format', function () {
    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('price')])
        // ->fill(['price' => 'test']);
        ->fill(['price' => new Money(12345600, new Currency('USD'))]);

    expect($component->getState()['price']->getAmount())->toEqual('12345600');
});

it('accepts null state and returns null', function () {
    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('price')])
        ->fill(['price' => null]);

    expect($component->getState()['price'])->amount->toBeNull();
});

it('triggers exception for non-numeric state', function () {
    $this->expectException(ParserException::class);

    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('price')])
        ->fill(['price' => 'non_numeric']);

    $component->getState();
});

/*
it('sets currency symbol placement after with global config', function () {
    config(['filament-money-field.form_currency_symbol_placement' => 'after']);

    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('price')])
        ->fill(['price' => 20]);

    /** @var MoneyInput $field * /
    $field = $component->getComponent('data.price');
    expect($field->getSuffixLabel())->toEqual('$');
    expect($field->getPrefixLabel())->toBeNull();
});
*/

it('sets currency symbol placement before with global config', function () {
    config(['filament-money-field.form_currency_symbol_placement' => 'before']);

    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('price')])
        ->fill(['price' => 20]);

    $field = $component->getComponent('data.price');
    expect($field->getPrefixLabel())->toEqual('$');
    expect($field->getSuffixLabel())->toBeNull();
});

/*
it('hides currency symbol with global config', function () {
    config(['filament-money-field.form_currency_symbol_placement' => 'hidden']);

    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('price')])
        ->fill(['price' => 20]);

    $field = $component->getComponent('data.price');
    expect($field->getPrefixLabel())->toBeNull();
    expect($field->getSuffixLabel())->toBeNull();
});
*/

/*
it('sets currency symbol placement after with on field config', function () {
    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('price')->symbolPlacement('after')])
        ->fill(['price' => 20]);

    $field = $component->getComponent('data.price');

    //dd($field);
    expect($field->getSuffixLabel())->toEqual('$');
    expect($field->getPrefixLabel())->toBeNull();
});

it('sets currency symbol placement before with on field config', function () {
    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('price')->symbolPlacement('before')])
        ->fill(['price' => 20]);

    $field = $component->getComponent('data.price');
    expect($field->getPrefixLabel())->toEqual('$');
    expect($field->getSuffixLabel())->toBeNull();
});

it('hides currency symbol with on field config', function () {
    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('price')->symbolPlacement('hidden')])
        ->fill(['price' => 20]);

    $field = $component->getComponent('data.price');
    expect($field->getPrefixLabel())->toBeNull();
    expect($field->getSuffixLabel())->toBeNull();
});

it('throws exception when currency symbol placement in invalid on field', function () {
    $this->expectException(\InvalidArgumentException::class);

    ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('price')->symbolPlacement('invalid')])
        ->fill(['price' => 20]);
});
*/

it('makes input mask', function () {
    config(['filament-money-field.use_input_mask' => true]);

    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('price')])
        ->fill(['price' => 20]);

    $this->assertStringContainsString('money($input', $component->getComponent('data.price')->getMask()->toHtml());
});

it('validates min and max values', function () {
    expect(validationTester((new MoneyInput('totalAmount'))->required()->minValue(100)->maxValue(10000), 20))->toBeTrue();

    validationTester((new MoneyInput('amount'))->required()->minValue(100)->maxValue(10000), 20, function (ValidationException $exception, MoneyInput $field) {
        expect($exception->validator->failed()[$field->getStatePath()])->toHaveKey(MinValueRule::class);
        expect($exception->validator->errors()->toArray()[$field->getStatePath()][0])->toEqual('The Amount must be less than or equal to 100.00.');
    });

    validationTester((new MoneyInput('totalAmount'))->required()->minValue(100)->maxValue(10000), 200, function (ValidationException $exception, MoneyInput $field) {
        expect($exception->validator->failed()[$field->getStatePath()])->toHaveKey(MaxValueRule::class);
        expect($exception->validator->errors()->toArray()[$field->getStatePath()][0])->toEqual('The Total Amount must be less than or equal to 100.00.');
    });

    validationTester((new MoneyInput('totalAmount'))->required()->minValue(100)->maxValue(10000), 'random string', function (ValidationException $exception, MoneyInput $field) {
        $failed = $exception->validator->failed()[$field->getStatePath()];

        expect($failed)->toHaveKey(MinValueRule::class);
        expect($failed)->toHaveKey(MaxValueRule::class);
        expect($exception->validator->errors()->toArray()[$field->getStatePath()][0])->toEqual('The Total Amount must be a valid numeric value.');
    });
});

it('throws exception with unsupported currency', function () {
    $this->expectException(UnsupportedCurrency::class);
    validationTester((new MoneyInput('totalAmount'))->currency('SOMETHING'), 20);
});

it('allows label to be overrided', function () {
    $field = (new MoneyInput('price'))->label('Custom Label');

    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([$field])
        ->fill([$field->getName() => 45345]);

    $field = $component->getComponent('data.price');
    expect($field->getLabel())->toEqual('Custom Label');
});

it('resolves label closures', function () {
    $field = (new MoneyInput('price'))->label(function () {
        return 'Custom Label in Closure';
    });

    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([$field])
        ->fill([$field->getName() => 45345]);

    $field = $component->getComponent('data.price');
    expect($field->getLabel())->toEqual('Custom Label in Closure');
});

it('sets decimals on field', function () {
    $field     = (new MoneyInput('price'))->decimals(1);
    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([$field])
        ->fill([$field->getName() => 2345345]);
    expect($component->getState()['price']->getAmount())->toEqual('2345345');

    $field     = (new MoneyInput('price'))->decimals(3);
    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([$field])
        ->fill([$field->getName() => 2345345]);
    expect($component->getState()['price']->getAmount())->toEqual('2345345');

    $field     = (new MoneyInput('price'))->decimals(-2);
    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([$field])
        ->fill([$field->getName() => 2345345]);
    expect($component->getState()['price']->getAmount())->toEqual('2345345');
});

it('accepts form input money with money cast', function () {
    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([MoneyInput::make('price_cast')])
        ->fill(['price_cast' => new Money(12345600, new Currency('USD'))]);

    expect($component->getState()['price_cast']->getAmount())->toEqual('12345600');
});

it('allows setting a currency column', function () {
    $component = ComponentContainer::make(FormTestComponent::make())
        ->statePath('data')
        ->components([
            MoneyInput::make('price')
                ->currencyColumn('price_currency')->in,
        ])
        ->fill(['price' => new Money(123456, new Currency('EUR'))]);

    expect($component->getState()['price']->getAmount())->toEqual('12345600');
    expect($component->getState()['price']->getCurrency())->toEqual('EUR');
});
