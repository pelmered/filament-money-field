<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Orchestra\Testbench\Factories\UserFactory;
use Pelmered\FilamentMoneyField\Tests\Support\Models\Post;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // AutoLogin signs in the first user, so the panel needs exactly one.
        UserFactory::new()->create([
            'name'  => 'Demo User',
            'email' => 'demo@example.com',
        ]);

        Post::factory()->count(25)->create();

        // The factory tops out around $100, which short() has nothing to
        // abbreviate. These give it something to render as "$1.23M".
        Post::factory()->create([
            'title'           => 'Big ticket (USD / EUR)',
            'price'           => 123_456_789,
            'price_currency'  => 'USD',
            'amount'          => 9_876_543_210,
            'amount_currency' => 'EUR',
        ]);

        Post::factory()->create([
            'title'           => 'Big ticket (SEK)',
            'price'           => 4_200_000_000,
            'price_currency'  => 'SEK',
            'amount'          => 1_500_000,
            'amount_currency' => 'SEK',
        ]);
    }
}
