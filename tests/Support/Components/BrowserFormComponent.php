<?php

declare(strict_types=1);

namespace Pelmered\FilamentMoneyField\Tests\Support\Components;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Money\Currency as MoneyCurrency;
use Money\Money;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;

/**
 * Full-page renderable counterpart to FormTestComponent, used by the browser
 * tests. The other test components are never rendered to HTML, so they have no
 * render() method or view.
 */
class BrowserFormComponent extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'price' => new Money(123456, new MoneyCurrency('USD')),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->components([
                MoneyInput::make('price')
                    ->currency('USD')
                    ->locale('en_US'),
            ]);
    }

    public function render(): View
    {
        return view('money-tests::browser-form');
    }
}
