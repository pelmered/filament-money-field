<?php
namespace Pelmered\FilamentMoneyField\Tests\Components;


use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Filament\Forms\Form;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;

class FormTestComponent extends Component implements HasForms
{
    use InteractsWithForms;

    public $data;
    public $form;

    public static function make(): static
    {
        return new static();
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                MoneyInput::make('price')
                    ->minValue(100)
                    ->maxValue(1000)
            ]);
    }

    public function mount(): void
    {
        //$this->form->fill();
    }

    public function data($data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }
}
