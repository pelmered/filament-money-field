<?php

uses(\Pelmered\FilamentMoneyField\Tests\TestCase::class);
use Filament\Infolists\ComponentContainer;
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;
use Pelmered\FilamentMoneyField\Tests\Components\InfolistTestComponent;


it('formats infolist money in usd', function () {
    $entry = MoneyEntry::make('price');

    $component = ComponentContainer::make(InfolistTestComponent::make())
        ->components([$entry])
        ->state([$entry->getName() => 100000000]);

    $entry = $component->getComponent('price');

    expect($entry->formatState($entry->getState()))->toEqual('$1,000,000.00');
});

it('formats infolist money in sek', function () {
    $entry = MoneyEntry::make('price')->currency('SEK')->locale('sv_SE');

    $component = ComponentContainer::make(InfolistTestComponent::make())
        ->components([$entry])
        ->state([$entry->getName() => 1000000]);

    $entry = $component->getComponent('price');

    expect($entry->formatState($entry->getState()))->toEqual(replaceNonBreakingSpaces('10 000,00 kr'));
});

it('formats infolist money in short format in USD', function () {
    $entry = MoneyEntry::make('price')->short();

    $component = ComponentContainer::make(InfolistTestComponent::make())
        ->components([$entry])
        ->state([$entry->getName() => 123456789]);

    $entry = $component->getComponent('price');

    expect($entry->formatState($entry->getState()))->toEqual(replaceNonBreakingSpaces('$1.23M'));
});

it('formats infolist money in short format in sek', function () {
    $entry = MoneyEntry::make('price')->short()->currency('SEK')->locale('sv_SE');

    $component = ComponentContainer::make(InfolistTestComponent::make())
        ->components([$entry])
        ->state([$entry->getName() => 123456]);

    $entry = $component->getComponent('price');

    expect($entry->formatState($entry->getState()))->toEqual(replaceNonBreakingSpaces('1,23K kr'));
});

it('formats infolist money in sek with no decimals', function () {
    $entry = MoneyEntry::make('price')->currency('SEK')->locale('sv_SE')->decimals(0);

    $component = ComponentContainer::make(InfolistTestComponent::make())
        ->components([$entry])
        ->state([$entry->getName() => 1000000]);

    $entry = $component->getComponent('price');

    expect($entry->formatState($entry->getState()))->toEqual(replaceNonBreakingSpaces('10 000 kr'));
});
