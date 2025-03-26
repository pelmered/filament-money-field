<?php

use Money\Currency as MoneyCurrency;

dataset('currency data', [
    'USD' => [
        'USD',
        [
            'currencyObject' => new MoneyCurrency('USD'),
            'currencyCode'   => 'USD',
            'currencyName'   => 'US Dollar',
            'minorUnit'      => 2,
        ],
    ],
    'EUR' => [
        'EUR',
        [
            'currencyObject' => new MoneyCurrency('EUR'),
            'currencyCode'   => 'EUR',
            'currencyName'   => 'Euro',
            'minorUnit'      => 2,
        ],
    ],
    'SEK' => [
        'SEK',
        [
            'currencyObject' => new MoneyCurrency('SEK'),
            'currencyCode'   => 'SEK',
            'currencyName'   => 'Swedish Krona',
            'minorUnit'      => 2,
        ],
    ],
    'PHP' => [
        'PHP',
        [
            'currencyObject' => new MoneyCurrency('PHP'),
            'currencyCode'   => 'PHP',
            'currencyName'   => 'Philippine Peso',
            'minorUnit'      => 2,
        ],
    ],
    'INR' => [
        'INR',
        [
            'currencyObject' => new MoneyCurrency('INR'),
            'currencyCode'   => 'INR',
            'currencyName'   => 'Indian Rupee',
            'minorUnit'      => 2,
        ],
    ],
]);
