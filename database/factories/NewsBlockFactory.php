<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\News;

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
        $types = ['text', 'image', 'text_image_left', 'text_image_right'];

        $selectedType = $this->faker->randomElement($types);

        return [
            'news_id' => News::factory(),
            'type' => $selectedType,
            'text_content' => in_array($selectedType, ['text', 'text_image_left', 'text_image_right']) ? $this->faker->paragraphs(mt_rand(1,5), true) : null,
            'image_path' => in_array($selectedType, ['image', 'text_image_left', 'text_image_right']) ? 'https://picsum.photos/seed/' . fake()->word() . '/800/600' : null,
            'position' => $this->faker->numberBetween(1, 10),
        ];
    }
}
