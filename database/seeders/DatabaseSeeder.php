<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\NewsBlock;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $users = User::factory(5)->create();
        $users->push($testUser);

        foreach ($users as $user) {
            News::factory(5)->create(['user_id' => $user->id])->each(function ($news) {
                $blocksCount = mt_rand(2, 5);
                for ($i = 0; $i < $blocksCount; $i++) {
                    NewsBlock::factory()->create([
                        'news_id' => $news->id,
                        'position' => $i,
                    ]);
                }
            });
        }

    }
}
