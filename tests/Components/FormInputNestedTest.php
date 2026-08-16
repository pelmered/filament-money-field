<?php

use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Pelmered\FilamentMoneyField\Exceptions\MissingMoneyCast;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Tests\Support\Components\FormTestComponent;
use Pelmered\FilamentMoneyField\Tests\Support\Models\Post;
use Pelmered\FilamentMoneyField\Tests\Support\Models\PostWithoutCasts;

it('checks the cast for a field nested in a section', function (): void {
    $post     = new Post(['title' => 'Test']);
    $livewire = FormTestComponent::make();

    $schema = Schema::make($livewire)
        ->model($post)
        ->statePath('data')
        ->components([
            Section::make()->schema([
                MoneyInput::make('price')->currency('EUR')->locale('pt_PT'),
            ]),
        ]);

    $schema->fill();
    $livewire->data['price'] = '123,45';

    $post->fill($schema->getState());
    $post->save();

    expect($post->getRawOriginal('price'))->toBe(12345);
});

it('reports a missing cast through a section too', function (): void {
    $livewire = FormTestComponent::make();

    $schema = Schema::make($livewire)
        ->model(PostWithoutCasts::class)
        ->statePath('data')
        ->components([
            Section::make()->schema([
                MoneyInput::make('price')->currency('EUR')->locale('pt_PT'),
            ]),
        ]);

    $schema->fill();
    $livewire->data['price'] = '123,45';

    $schema->getState();
})->throws(MissingMoneyCast::class);

it('does not check the model cast for a field inside an array repeater', function (): void {
    $post     = new Post(['title' => 'Test']);
    $livewire = FormTestComponent::make();

    $schema = Schema::make($livewire)
        ->model($post)
        ->statePath('data')
        ->components([
            Repeater::make('lines')->schema([
                MoneyInput::make('unit_price')->currency('EUR')->locale('pt_PT'),
            ]),
        ]);

    $schema->fill(['lines' => [['unit_price' => null]]]);

    $itemKey                                         = array_key_first($livewire->data['lines']);
    $livewire->data['lines'][$itemKey]['unit_price'] = '123,45';

    $state = $schema->getState();

    expect($state['lines'])->toHaveCount(1)
        ->and(reset($state['lines'])['unit_price'])->toBeInstanceOf(Money\Money::class);
});
