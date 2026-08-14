<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Browser tests for MoneyInput
|--------------------------------------------------------------------------
|
| These cover the parts of the field that only exist once Livewire, Alpine and
| Filament's JavaScript have booted: the input is rendered without a value
| attribute and is hydrated client-side, the currency switcher is an Alpine
| dropdown, and the input mask runs entirely in the browser.
|
| Selectors are written as attribute selectors because Filament ids contain a
| period ("form.price"), which the browser plugin would otherwise treat as a
| CSS class selector.
|
*/

const PRICE_INPUT = '[id="form.price"]';

it('hydrates the input with the localized money value', function () {
    visit('/money-form')
        ->assertValue(PRICE_INPUT, '1,234.56');
});

it('renders the currency symbol before the amount by default', function () {
    visit('/money-form')
        ->assertSeeIn('.fi-input-wrp-prefix .fi-input-wrp-label', '$')
        ->assertMissing('.fi-input-wrp-suffix .fi-input-wrp-label');
});

it('renders the currency symbol after the amount when configured', function () {
    config()->set('filament-money-field.form_currency_symbol_placement', 'after');

    visit('/money-form')
        ->assertSeeIn('.fi-input-wrp-suffix .fi-input-wrp-label', '$')
        ->assertMissing('.fi-input-wrp-prefix .fi-input-wrp-label');
});

it('renders no currency symbol when the placement is hidden', function () {
    config()->set('filament-money-field.form_currency_symbol_placement', 'hidden');

    visit('/money-form')
        ->assertMissing('.fi-input-wrp-label');
});

it('renders the currency switcher action', function () {
    visit('/money-form')
        ->assertVisible('[aria-label="Change currency"]');
});

it('renders no currency switcher when disabled by config', function () {
    config()->set('filament-money-field.currency_switcher_enabled_default', false);

    visit('/money-form')
        ->assertMissing('[aria-label="Change currency"]');
});

it('groups thousands while typing when the input mask is enabled', function () {
    config()->set('filament-money-field.use_input_mask', true);

    visit('/money-form')
        ->clear(PRICE_INPUT)
        ->type(PRICE_INPUT, '9876543.21')
        ->assertValue(PRICE_INPUT, '9,876,543.21');
});

it('does not mask the input when the mask is disabled', function () {
    config()->set('filament-money-field.use_input_mask', false);

    visit('/money-form')
        ->clear(PRICE_INPUT)
        ->type(PRICE_INPUT, '9876543.21')
        ->assertValue(PRICE_INPUT, '9876543.21');
});

it('renders the field without javascript errors', function () {
    visit('/money-form')
        ->assertNoJavaScriptErrors();
});
