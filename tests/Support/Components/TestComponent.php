<?php

namespace Pelmered\FilamentMoneyField\Tests\Support\Components;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;

class TestComponent extends Component implements HasForms
{
    use InteractsWithForms;

    public $data = [];

    public function __construct()
    {
    }

    public static function make(): static
    {
        return new static;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                MoneyInput::make('money'),
                MoneyInput::make('price'),
            ]);
    }

    public function mount(): void
    {
        // $this->form->fill();
    }

    public function getData()
    {
        return $this->data;
    }
}
