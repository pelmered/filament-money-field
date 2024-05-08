<?php
namespace Pelmered\FilamentMoneyField\Tests\Components;


use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Livewire\Component;
use Filament\Forms\Form;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;

class InfolistTestComponent extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public static function make(): static
    {
        return new static();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state([])
            ->schema([
                MoneyEntry::make('amount')
                    ->currency('SEK')
                    ->locale('sv_SE')
                    ->label('Amount'),
            ]);
    }

    /*
    public function render(): View
    {
        return view('infolists.fixtures.actions');
    }
    */
}
