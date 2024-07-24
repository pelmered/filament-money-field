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
    | Hide decimals if zero
    |---------------------------------------------------------------------------
    |
    | Hide the decimal part of the amount if it is zero.
    | For example, $23.00 becomes $23.
    |
    */
    'hide_decimals_if_zero' => env('MONEY_HIDE_DECIMALS_IF_ZERO', false),

    /*
    |---------------------------------------------------------------------------
    | International currency symbol
    |---------------------------------------------------------------------------
    |
    | Use international currency symbols.
    | For example: USD, EUR, SEK instead of $, €, kr etc.
    |
    */
    'intl_currency_symbol' => env('MONEY_INTL_CURRENCY_SYMBOL', false),

    /*
    |---------------------------------------------------------------------------
    | Currency symbol placement
    |---------------------------------------------------------------------------
    |
    | Where the dunit should be on form fields. Options are 'before' (prefix), 'after' (suffix) or 'none'.
    | Note: In most non-English speaking European countries,
    | the currency symbol is after the amount and is preceded by a space (as in "10 €")
    |
    */
    'form_currency_symbol_placement' => env('MONEY_UNIT_PLACEMENT', 'before'),



];
