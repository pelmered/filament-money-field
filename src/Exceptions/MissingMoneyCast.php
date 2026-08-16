<?php

declare(strict_types=1);

namespace Pelmered\FilamentMoneyField\Exceptions;

use RuntimeException;

class MissingMoneyCast extends RuntimeException
{
    public function __construct(string $model, string $attribute)
    {
        parent::__construct(strtr(
            'Attribute [{attribute}] on model [{model}] is not cast to a Money object, so it cannot be saved. '
            .('Add \'{attribute}\' => ' . \Pelmered\FilamentMoneyField\Casts\MoneyCast::class . '::class and ')
            .('\'{attribute}{suffix}\' => ' . \Pelmered\FilamentMoneyField\Casts\CurrencyCast::class . '::class to the $casts of the model. ')
            .'See https://github.com/pelmered/filament-money-field#casts',
            [
                '{attribute}' => $attribute,
                '{model}'     => $model,
                '{suffix}'    => (string) config('larapara.currency_column_suffix', '_currency'),
            ]
        ));
    }
}
