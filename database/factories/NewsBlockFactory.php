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

        $content = ['type' => $selectedType];

        if(in_array($selectedType, ['text', 'text_image_left', 'text_image_right'])){
            $content['text'] = $this->faker->paragraphs(mt_rand(1,5), true);
        }

        if(in_array($selectedType, ['image', 'text_image_left', 'text_image_right'])){
            $content['image'] = 'news_blocks_images/placeholder.jpg';
        }

        return [
            'news_id' => News::factory(),
            'content' => $content,
            'position' => $this->faker->numberBetween(1, 10),
        ];
    }
}
