<?php

namespace Pelmered\FilamentMoneyField\Commands;

use Illuminate\Console\Command;
use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\Currencies\CurrencyRepository;

class CacheCommand extends Command
{
    protected $signature = 'money:cache';

    public function handle(): void {
        $currencies = CurrencyRepository::getAvailableCurrencies();

        $this->info($currencies->count() . ' Currencies cached.');

        if ($this->option('verbose')) {
            $this->table(
                ['Name', 'Code', 'Minor Unit Decimals'],
                $currencies->map(fn (Currency $currency): array => [$currency->name, $currency->code, $currency->minorUnit])
            );
        }
    }
}
