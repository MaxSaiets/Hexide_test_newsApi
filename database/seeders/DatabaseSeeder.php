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


        $otherUsers = User::factory(5)->create();
        $allUsers = $otherUsers->concat([$testUser]);

        foreach ($allUsers as $user) {

            $newsCount = ($user->id === $testUser->id) ? 10 : 5;
            
            News::factory($newsCount)
                ->for($user)
                ->create()
                ->each(function (News $news) {
                    NewsBlock::factory(rand(2, 6))
                        ->sequence(fn($sequence) => ['position' => $sequence->index])
                        ->for($news)
                        ->create();
                });
        }

    }
}
