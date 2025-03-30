<?php

use Illuminate\Support\Facades\Cache;

test('cache command caches currencies', function (): void {

    Cache::forget('filament_money_currencies');

    $this->artisan('money:cache')
         ->expectsOutput('3 Currencies cached.')
         ->assertExitCode(0);

    expect(Cache::has('filament_money_currencies'))->toBeTrue();
});

test('clear cache command removes currencies from cache', function (): void {

    $this->artisan('money:cache')
         ->assertExitCode(0);

    expect(Cache::has('filament_money_currencies'))->toBeTrue();

    $this->artisan('money:clear')
         ->expectsOutput('Currencies cache cleared.')
         ->assertExitCode(0);

    expect(Cache::has('filament_money_currencies'))->toBeFalse();
});

/*
test('optimize command also adds currencies to cache', function () {

    $this->artisan('optimize')
         ->assertExitCode(0);

    expect(Cache::has('filament_money_currencies'))->toBeTrue();
});
*/
