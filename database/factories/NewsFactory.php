<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(5);
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->numberBetween(1, 100),
            'short_description' => $this->faker->text(260),
            'image' => fake()->boolean(20) ? null : 'https://picsum.photos/seed/' . fake()->word() . '/800/600',
            'is_published' => $this->faker->boolean(70),
            'published_at' => $this->faker->dateTime(),
            'user_id' => User::factory(),
        ];
    }
}
