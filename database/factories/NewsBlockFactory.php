<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\News;
use App\Enums\NewsBlockType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsBlock>
 */
class NewsBlockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $selectedType = $this->faker->randomElement(NewsBlockType::cases());

        return [
            'news_id' => News::factory(),
            'type' => $selectedType,
            'text_content' => $selectedType->hasText() ? $this->faker->paragraphs(mt_rand(1,5), true) : null,
            'image_path' => $selectedType->hasImage() ? 'https://picsum.photos/seed/' . fake()->word() . '/800/600' : null,
            'position' => $this->faker->numberBetween(1, 10),
        ];
    }
}
