<?php

namespace Pelmered\FilamentMoneyField\Tests;

use Filament\Infolists\ComponentContainer;
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;
use Pelmered\FilamentMoneyField\Tests\Components\InfolistTestComponent;

class MoneyEntryTest extends TestCase
{
    public function testInfoListMoneyFormat(): void
    {
        $entry = MoneyEntry::make('price');

        $component = ComponentContainer::make(InfolistTestComponent::make())
            ->components([$entry])
            ->state([$entry->getName() => 100000000]);

        $entry = $component->getComponent('price');

        $this->assertEquals('$1,000,000.00', $entry->formatState($entry->getState()));
    }

    public function testInfoListMoneyFormatSek(): void
    {
        $entry = MoneyEntry::make('price')->currency('SEK')->locale('sv_SE');

        $component = ComponentContainer::make(InfolistTestComponent::make())
            ->components([$entry])
            ->state([$entry->getName() => 1000000]);

        $entry = $component->getComponent('price');

        $this->assertEquals(
            static::replaceNonBreakingSpaces('10 000,00 kr'),
            $entry->formatState($entry->getState())
        );
    }

    public function testInfoListMoneyFormatShort(): void
    {
        $entry = MoneyEntry::make('price')->short();

        $component = ComponentContainer::make(InfolistTestComponent::make())
            ->components([$entry])
            ->state([$entry->getName() => 123456789]);

        $entry = $component->getComponent('price');

        $this->assertEquals(
            static::replaceNonBreakingSpaces('$1.23M'),
            $entry->formatState($entry->getState())
        );
    }

    public function testInfoListMoneyFormatShortSek(): void
    {
        $entry = MoneyEntry::make('price')->short()->currency('SEK')->locale('sv_SE');

        $component = ComponentContainer::make(InfolistTestComponent::make())
            ->components([$entry])
            ->state([$entry->getName() => 123456]);

        $entry = $component->getComponent('price');

        $this->assertEquals(
            static::replaceNonBreakingSpaces('1,23K kr'),
            $entry->formatState($entry->getState())
        );
    }

    public function testInfoListMoneyFormatSekNoDecimals(): void
    {
        $entry = MoneyEntry::make('price')->currency('SEK')->locale('sv_SE')->decimals(0);

        $component = ComponentContainer::make(InfolistTestComponent::make())
            ->components([$entry])
            ->state([$entry->getName() => 1000000]);

        $entry = $component->getComponent('price');

        $this->assertEquals(
            static::replaceNonBreakingSpaces('10 000 kr'),
            $entry->formatState($entry->getState())
        );
    }
}
