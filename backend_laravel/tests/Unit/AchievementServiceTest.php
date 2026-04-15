<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Purchase;
use App\Models\Achievement;
use App\Services\AchievementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AchievementService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AchievementService();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_unlocks_first_purchase_achievement_after_one_purchase()
    {
        // Create one purchase
        Purchase::factory()->create(['user_id' => $this->user->id]);

        // Process achievements
        $newAchievements = $this->service->processAchievements($this->user);

        // Assert first purchase achievement is unlocked
        $this->assertCount(1, $newAchievements);
        $this->assertEquals('first_purchase', $newAchievements[0]['key']);
        $this->assertTrue($this->user->achievements()->where('key', 'first_purchase')->exists());
    }

    /** @test */
    public function it_unlocks_multiple_achievements_when_conditions_are_met()
    {
        // Create 3 purchases
        Purchase::factory()->count(3)->create(['user_id' => $this->user->id]);

        // Process achievements
        $newAchievements = $this->service->processAchievements($this->user);

        // Should unlock 'first_purchase' and 'returning_customer'
        $this->assertCount(2, $newAchievements);
        $achievementKeys = array_column($newAchievements, 'key');
        $this->assertContains('first_purchase', $achievementKeys);
        $this->assertContains('returning_customer', $achievementKeys);
    }

    /** @test */
    public function it_does_not_unlock_already_unlocked_achievements()
    {
        // Create purchase and unlock achievement
        Purchase::factory()->create(['user_id' => $this->user->id]);
        $this->service->processAchievements($this->user);
        $initialCount = $this->user->achievements()->count();

        // Create another purchase (total: 2)
        Purchase::factory()->create(['user_id' => $this->user->id]);
        $this->service->processAchievements($this->user);

        // Should still only have 1 achievement
        $this->assertEquals($initialCount, $this->user->achievements()->count());
    }

    /** @test */
    public function it_returns_correct_current_badge_for_beginner()
    {
        // No achievements = Beginner badge
        $badge = $this->service->getCurrentBadge($this->user);

        $this->assertEquals('beginner', $badge['key']);
        $this->assertEquals('Beginner', $badge['name']);
    }

    /** @test */
    public function it_returns_correct_badge_after_unlocking_achievements()
    {
        // Create 5 purchases and unlock achievements
        Purchase::factory()->count(5)->create(['user_id' => $this->user->id]);
        $this->service->processAchievements($this->user);

        // Should have Silver badge (3 achievements unlocked)
        $badge = $this->service->getCurrentBadge($this->user);

        $this->assertEquals('silver', $badge['key']);
    }

    /** @test */
    public function it_calculates_remaining_achievements_for_next_badge()
    {
        // No achievements - need 1 for Bronze
        $remaining = $this->service->getRemainingForNextBadge($this->user);
        $this->assertEquals(1, $remaining);

        // Create 1 purchase
        Purchase::factory()->create(['user_id' => $this->user->id]);
        $this->service->processAchievements($this->user);

        // Now need 2 more for Silver (3 total)
        $this->user->refresh();
        $remaining = $this->service->getRemainingForNextBadge($this->user);
        $this->assertEquals(2, $remaining);
    }

    /** @test */
    public function it_calculates_progress_percentage_correctly()
    {
        // Beginner -> Bronze: 0% progress
        $progress = $this->service->getProgressPercentage($this->user);
        $this->assertEquals(0, $progress);

        // Unlock 1 achievement (Bronze badge, progress toward Silver)
        Purchase::factory()->create(['user_id' => $this->user->id]);
        $this->service->processAchievements($this->user);
        $this->user->refresh();

        // Bronze (1) -> Silver (3): 1/2 progress should be ~0%
        $progress = $this->service->getProgressPercentage($this->user);
        $this->assertEquals(0, $progress);

        // Unlock 2 more achievements
        Purchase::factory()->count(2)->create(['user_id' => $this->user->id]);
        $this->service->processAchievements($this->user);
        $this->user->refresh();

        // Now at 3 achievements = Silver
        $badge = $this->service->getCurrentBadge($this->user);
        $this->assertEquals('silver', $badge['key']);
    }

    /** @test */
    public function it_returns_next_available_achievements()
    {
        // Should return first 3 achievements
        $next = $this->service->getNextAvailableAchievements($this->user);

        $this->assertCount(3, $next);
        $this->assertEquals('first_purchase', $next[0]['key']);
    }

    /** @test */
    public function it_returns_all_achievements_with_status()
    {
        Purchase::factory()->create(['user_id' => $this->user->id]);
        $this->service->processAchievements($this->user);

        $all = $this->service->getAllAchievementsWithStatus($this->user);

        // First should be unlocked
        $firstPurchase = $all->firstWhere('key', 'first_purchase');
        $this->assertTrue($firstPurchase['unlocked']);

        // Others should still be locked
        $shopaholic = $all->firstWhere('key', 'shopaholic');
        $this->assertFalse($shopaholic['unlocked']);
    }

    /** @test */
    public function it_tracks_purchase_progress_between_gold_and_platinum_badges()
    {
        // 28 purchases unlock up to 25-purchase achievement (Gold badge),
        // and should show partial progress toward 50-purchase (Platinum).
        Purchase::factory()->count(28)->create(['user_id' => $this->user->id]);
        $this->service->processAchievements($this->user);
        $this->user->refresh();

        $badge = $this->service->getCurrentBadge($this->user);
        $this->assertEquals('gold', $badge['key']);

        $progress = $this->service->getProgressPercentage($this->user);

        // From 25 -> 50 purchases, 28 should be about 12% progress.
        $this->assertGreaterThanOrEqual(10, $progress);
        $this->assertLessThanOrEqual(15, $progress);
    }
}
