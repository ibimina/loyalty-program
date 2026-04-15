<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Purchase;
use App\Services\AchievementService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $achievementService = new AchievementService();

        // Create demo user
        $demoUser = User::factory()->create([
            'name' => 'Ibimina Hart',
            'email' => 'ibimina.c.hart@gmail.com',
        ]);

        // Create 4 purchases for demo user (will unlock some achievements)
        Purchase::factory()->count(4)->create(['user_id' => $demoUser->id]);
        $achievementService->processAchievements($demoUser);

        $this->command->info("Created demo user:  ibimina.c.hart@gmail.com");
        $this->command->info("Demo user has {$demoUser->purchases()->count()} purchases");
        $this->command->info("Demo user has {$demoUser->achievements()->count()} achievements");

        // Create additional test users with varying progress
        $users = [
            ['name' => 'New User', 'email' => 'new@example.com', 'purchases' => 0],
            ['name' => 'Bronze User', 'email' => 'bronze@example.com', 'purchases' => 1],
            ['name' => 'Silver User', 'email' => 'silver@example.com', 'purchases' => 5],
            ['name' => 'Gold User', 'email' => 'gold@example.com', 'purchases' => 10],
        ];

        foreach ($users as $userData) {
            $user = User::factory()->create([
                'name' => $userData['name'],
                'email' => $userData['email'],
            ]);

            if ($userData['purchases'] > 0) {
                Purchase::factory()->count($userData['purchases'])->create(['user_id' => $user->id]);
                $achievementService->processAchievements($user);
            }

            $this->command->info("Created {$userData['name']}: {$userData['email']} with {$userData['purchases']} purchases");
        }
    }
}
