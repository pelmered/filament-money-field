<?php

namespace Pelmered\FilamentMoneyField\Currencies;

use Illuminate\Support\Collection;
use PhpStaticAnalysis\Attributes\Returns;
use PhpStaticAnalysis\Attributes\TemplateExtends;

#[TemplateExtends('Collection<string, Currency>')]
class CurrencyCollection extends Collection
{
    #[Returns('array<string, string>')]
    public function toSelectArray(): array
    {
        return $this->mapWithKeys(function (Currency $currency) {
            return [
                $currency->code => $currency->name,
            ];
        })->toArray();
    }
}
