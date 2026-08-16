<?php

use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Money\Currency as MoneyCurrency;
use Money\Money;
use Pelmered\FilamentMoneyField\Exceptions\MissingMoneyCast;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Tests\Support\Components\FormTestComponent;
use Pelmered\FilamentMoneyField\Tests\Support\Models\Post;
use Pelmered\FilamentMoneyField\Tests\Support\Models\PostWithoutCasts;

function moneyFormSchema(Model|string $model, string $input = '123,45'): Schema
{
    $livewire = FormTestComponent::make();

    $schema = Schema::make($livewire)
        ->model($model)
        ->statePath('data')
        ->components([
            MoneyInput::make('price')
                ->currency('EUR')
                ->locale('pt_PT'),
        ]);

    $schema->fill();
    $livewire->data['price'] = $input;

    return $schema;
}

// https://github.com/pelmered/filament-money-field/issues/102
it('saves the form value as minor units and a currency code', function (): void {
    $post = new Post(['title' => 'Test']);

    $post->fill(moneyFormSchema($post)->getState());
    $post->save();

    expect($post->getRawOriginal('price'))->toBe(12345)
        ->and($post->getRawOriginal('price_currency'))->toBe('EUR');
});

it('tells you which cast is missing instead of letting a Money object hit the database', function (): void {
    moneyFormSchema(PostWithoutCasts::class)->getState();
})->throws(
    MissingMoneyCast::class,
    'Attribute [price] on model [Pelmered\FilamentMoneyField\Tests\Support\Models\PostWithoutCasts] is not cast to a Money object, '
    ."so it cannot be saved. Add 'price' => \\Pelmered\\FilamentMoneyField\\Casts\\MoneyCast::class and "
    ."'price_currency' => \\Pelmered\\FilamentMoneyField\\Casts\\CurrencyCast::class",
);

// The error the guard above replaces, kept to document why it exists.
it('cannot persist a Money object without a cast', function (): void {
    $post        = new PostWithoutCasts(['title' => 'Test']);
    $post->price = new Money(12345, new MoneyCurrency('EUR'));

    $post->save();
})->throws(Error::class, 'Object of class Money\Money could not be converted to string');
