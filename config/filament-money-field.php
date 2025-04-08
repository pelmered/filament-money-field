<?php

return [
    /*
    |---------------------------------------------------------------------------
    | Default locale
    |---------------------------------------------------------------------------
    |
    | If not set, it will use the Laravel app locale.
    | For example: en_US, en_GB, sv_SE, etc.
    |
    */
    'default_locale' => env('MONEY_DEFAULT_LOCALE', 'en_US'),

    /*
    |---------------------------------------------------------------------------
    | Default currency
    |---------------------------------------------------------------------------
    |
    | The currency code to use if not set on the field.
    |
    */
    'use_input_mask' => env('MONEY_USE_INPUT_MASK', false),

    /*
    |---------------------------------------------------------------------------
    | Fraction digits
    |---------------------------------------------------------------------------
    |
    | The currency code to use if not set on the field.
    |
    */
    'decimal_digits' => env('MONEY_DECIMAL_DIGITS', 2),

    /*
    |---------------------------------------------------------------------------
    | Currency symbol placement
    |---------------------------------------------------------------------------
    |
    | Where the dunit should be on form fields. Options are 'before' (prefix), 'after' (suffix) or 'hidden'.
    | Note: In most non-English speaking European countries,
    | the currency symbol is after the amount and is preceded by a space (as in "10 €")
    |
    */
    'form_currency_symbol_placement' => env('MONEY_UNIT_PLACEMENT', 'before'),

    /*
    |---------------------------------------------------------------------------
    | Currency switcher enabled on fields by default
    |---------------------------------------------------------------------------
    |
    | Should the currency switcher be enabled on fields by default.
    | You can change this on a per-field basis with ->currencySwitcherEnabled() and ->currencySwitcherDisabled().
    */
    'currency_switcher_enabled_default' => env('MONEY_CURRENCY_SWITCHER_ENABLED', true),
];
