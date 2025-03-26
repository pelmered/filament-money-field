<?php

namespace Pelmered\FilamentMoneyField\Currencies;

use Illuminate\Support\Collection;
use PhpStaticAnalysis\Attributes\TemplateExtends;

#[TemplateExtends('Collection<string, Currency>')]
class CurrencyCollection extends Collection
{
    public function toSelectArray()
    {
        return $this->mapWithKeys(function (Currency $currency) {
            return [
                $currency->code => $currency->name,
            ];
        })->toArray();
    }
}
