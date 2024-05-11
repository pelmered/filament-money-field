<?php

namespace Pelmered\FilamentMoneyField\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Pelmered\FilamentMoneyField\Tests\Models\Post;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'author_id'    => random_int(1, 10),
            'content'      => $this->faker->paragraph(),
            'is_published' => $this->faker->boolean(),
            'tags'         => $this->faker->words(),
            'title'        => $this->faker->sentence(),
            'rating'       => $this->faker->numberBetween(1, 10),
            'price'        => $this->faker->numberBetween(100, 10000),
        ];
    }
}
