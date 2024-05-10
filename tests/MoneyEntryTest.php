<?php

namespace Pelmered\FilamentMoneyField\Tests;

use Filament\Infolists\ComponentContainer;
use JetBrains\PhpStorm\NoReturn;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;
use Pelmered\FilamentMoneyField\Tests\Components\FormTestComponent;
use Pelmered\FilamentMoneyField\Tests\Components\InfolistTestComponent;
use Pelmered\FilamentMoneyField\Tests\Models\Post;

class MoneyEntryTest extends TestCase
{
    public function testInfoListMoneyFormat(): void
    {
        $entry = MoneyEntry::make('price');

        $component = ComponentContainer::make(InfolistTestComponent::make())
                                       ->components([
                                           $entry,
                                       ])->state([$entry->getName() => 1000000]);

        $entry = $component->getComponent('price');

        $this->assertEquals('$10,000.00', $entry->formatState($entry->getState()));
    }
    public function testInfoListMoneyFormatSek(): void
    {
        $entry = MoneyEntry::make('price')->currency('SEK')->locale('sv_SE');

        $component = ComponentContainer::make(InfolistTestComponent::make())
                                       ->components([
                                           $entry,
                                       ])->state([$entry->getName() => 1000000]);

        $entry = $component->getComponent('price');

        $this->assertEquals(
            static::replaceNonBreakingSpaces('10 000,00 kr'),
            $entry->formatState($entry->getState())
        );
    }
}
