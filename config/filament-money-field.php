<?php

return [

    /*
    |---------------------------------------------------------------------------
    | Default locale
    |---------------------------------------------------------------------------
    |
    | If not set, it will use the Laravel app locale.
    |
    */
    'default_locale' => env('MONEY_DEFAULT_LOCALE', null),


    /*
    |---------------------------------------------------------------------------
    | Default currency
    |---------------------------------------------------------------------------
    |
    | The currency code to use if not set on the field.
    |
    */
    'default_currency' => env('MONEY_DEFAULT_CURRENCY', null),

];
