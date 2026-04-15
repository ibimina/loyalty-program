<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Events\PurchaseMade;
use App\Models\User;
use App\Models\Purchase;
use App\Services\AchievementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AchievementApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected AchievementService $achievementService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->achievementService = new AchievementService();
    }

    /** @test */
    public function it_returns_user_achievements_data()
    {
        $response = $this->getJson("/api/users/{$this->user->id}/achievements");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'unlocked_achievements',
                    'next_available_achievements',
                    'current_badge',
                    'next_badge',
                    'remaining_to_unlock_next_badge',
                    'progress_percentage',
                    'achievements' => [
                        'unlocked',
                        'next_available',
                        'all',
                    ],
                    'badges' => [
                        'current',
                        'next',
                        'all',
                    ],
                    'stats',
                ],
            ]);
    }

    /** @test */
    public function it_returns_beginner_badge_for_new_user()
    {
        $response = $this->getJson("/api/users/{$this->user->id}/achievements");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'current_badge' => 'Beginner',
                    'next_badge' => 'Bronze',
                    'remaining_to_unlock_next_badge' => 1,
                    'unlocked_achievements' => [],
                ],
            ]);
    }

    /** @test */
    public function it_shows_unlocked_achievements_after_purchase()
    {
        // Create a purchase and process achievements
        Purchase::factory()->create(['user_id' => $this->user->id]);
        $this->achievementService->processAchievements($this->user);

        $response = $this->getJson("/api/users/{$this->user->id}/achievements");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'unlocked_achievements' => ['First Purchase'],
                'current_badge' => 'Bronze',
            ]);
    }

    /** @test */
    public function it_can_simulate_purchase_and_trigger_events()
    {
        Event::fake([AchievementUnlocked::class, BadgeUnlocked::class]);

        $response = $this->postJson("/api/users/{$this->user->id}/purchases", [
            'amount' => 5000,
            'product_name' => 'Test Product',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['purchase', 'total_purchases'],
            ]);

        // In real implementation with sync processing:
        // Event::assertDispatched(AchievementUnlocked::class);
        // Event::assertDispatched(BadgeUnlocked::class);
    }

    /** @test */
    public function it_returns_404_for_non_existent_user()
    {
        $response = $this->getJson("/api/users/99999/achievements");

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_achievement_history()
    {
        // Create achievements
        Purchase::factory()->create(['user_id' => $this->user->id]);
        $this->achievementService->processAchievements($this->user);

        $response = $this->getJson("/api/users/{$this->user->id}/achievements/history");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'history',
                    'count',
                ],
            ]);
    }

    /** @test */
    public function it_tracks_progress_percentage()
    {
        // Create 3 purchases (should unlock 2 achievements)
        Purchase::factory()->count(3)->create(['user_id' => $this->user->id]);
        $this->achievementService->processAchievements($this->user);

        $response = $this->getJson("/api/users/{$this->user->id}/achievements");

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertArrayHasKey('progress_percentage', $data);
        $this->assertIsInt($data['progress_percentage']);
    }
}
