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
    | The currency ISO code to use if not set on the field.
    | For example: USD, EUR, SEK, etc.
    |
    */
    'default_currency' => env('MONEY_DEFAULT_CURRENCY', 'USD'),

    /*
    |---------------------------------------------------------------------------
    | Default currency
    |---------------------------------------------------------------------------
    |
    | The currency code to use if not set on the field.
    |
    */
    'use_input_mask' => env('MONEY_USE_INPUT_MASK', false),
];
