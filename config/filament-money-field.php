<?php

declare(strict_types=1);

return [
    /*
    |---------------------------------------------------------------------------
    | Default locale
    |---------------------------------------------------------------------------
    |
    | For example: en_US, en_GB, sv_SE, etc.
    | Leave this unset to follow the Laravel app locale.
    |
    */
    'default_locale' => env('MONEY_DEFAULT_LOCALE'),

    /*
    |---------------------------------------------------------------------------
    | Input mask
    |---------------------------------------------------------------------------
    |
    | Format the amount in the input field as you type. Experimental.
    |
    */
    'use_input_mask' => env('MONEY_USE_INPUT_MASK', false),

    /*
    |---------------------------------------------------------------------------
    | Fraction digits
    |---------------------------------------------------------------------------
    |
    | The number of decimals to display. Negative values mean significant
    | digits instead, so -2 displays 1234.56 as 1,200.
    |
    */
    'decimal_digits' => env('MONEY_DECIMAL_DIGITS', 2),

    /*
    |---------------------------------------------------------------------------
    | Currency symbol placement
    |---------------------------------------------------------------------------
    |
    | Where the unit should be on form fields. Options are 'before' (prefix), 'after' (suffix) or 'hidden'.
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

    /*
    |---------------------------------------------------------------------------
    | LaraPara overrides
    |---------------------------------------------------------------------------
    |
    | The currency handling itself lives in pelmered/larapara, which ships its
    | own config file. Anything set below wins over the matching key in
    | config/larapara.php, so a project only needs to publish this one file.
    |
    | Every key in this section is inherited from LaraPara unless you set it
    | here: leaving it null - which is also what you get by removing the key
    | from this file, or by leaving its MONEY_* variable out of .env - keeps
    | LaraPara's own value in charge, so a published config/larapara.php still
    | decides and nothing is silently overridden. The inherited value is noted
    | with each key below.
    |
    | The remaining LaraPara keys (currency_provider, currency_cast_to,
    | excluded_currencies, load_crypto_currencies, currency_cache) are advanced
    | and are only configurable in config/larapara.php.
    |
    */

    // ISO 4217 code, e.g. USD, EUR, SEK. Inherits 'USD'.
    'default_currency' => env('MONEY_DEFAULT_CURRENCY'),

    // Use ISO codes instead of symbols: USD instead of $, SEK instead of kr.
    // Inherits false.
    'intl_currency_symbol' => env('MONEY_INTL_CURRENCY_SYMBOL'),

    // Suffix of the companion currency column, so price -> price_currency.
    // Inherits '_currency'.
    'currency_column_suffix' => env('MONEY_CURRENCY_COLUMN_SUFFIX'),

    // Restrict the selectable currencies. An array here, or a comma separated
    // string in .env: MONEY_AVAILABLE_CURRENCIES="USD,EUR,SEK".
    // Inherits [], which allows every currency LaraPara knows about.
    'available_currencies' => env('MONEY_AVAILABLE_CURRENCIES'),

    // How the amount is stored: 'int' (minor units) or 'decimal'. Decides what
    // the money() migration macros create and how MoneyCast reads the column.
    // Inherits 'int'.
    'store' => [
        'format' => env('MONEY_STORE_FORMAT'),
    ],
];
