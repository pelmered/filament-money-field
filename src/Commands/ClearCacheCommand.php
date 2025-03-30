<?php

namespace Pelmered\FilamentMoneyField\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

class ClearCacheCommand extends Command
{
    protected $signature = 'money:clear';

    /**
     * @throws InvalidArgumentException
     */
    public function handle(): void {
        Cache::delete('filament_money_currencies');

        $this->info('Currencies cache cleared.');
    }
}
