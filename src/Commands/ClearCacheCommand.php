<?php

namespace Pelmered\FilamentMoneyField\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use PhpStaticAnalysis\Attributes\Throws;
use Psr\SimpleCache\InvalidArgumentException;

class ClearCacheCommand extends Command
{
    protected $signature = 'money:clear';

    #[Throws(InvalidArgumentException::class)]
    public function handle(): void
    {
        Cache::delete('filament_money_currencies');

        $this->info('Currencies cache cleared.');
    }
}
