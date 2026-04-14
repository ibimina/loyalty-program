<?php

namespace App\Services;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service responsible for processing achievements and badges.
 * 
 * This service implements a config-driven approach where achievements
 * are defined in config/achievements.php, making the system easily
 * extensible without code changes.
 */
class AchievementService
{
    protected Collection $purchaseAchievements;
    protected Collection $badges;

    public function __construct()
    {
        $this->purchaseAchievements = collect(config('achievements.purchase_achievements'));
        $this->badges = collect(config('achievements.badges'));
    }

    /**
     * Process achievements for a user after a purchase.
     * 
     * @param User $user
     * @return array Array of newly unlocked achievements
     */
    public function processAchievements(User $user): array
    {
        $purchaseCount = $user->purchases()->count();
        $unlockedAchievementKeys = $user->achievements()->pluck('key')->toArray();
        $newlyUnlocked = [];
        $previousBadge = $this->getCurrentBadge($user);

        // Check each purchase achievement
        foreach ($this->purchaseAchievements as $achievement) {
            // Skip if already unlocked
            if (in_array($achievement['key'], $unlockedAchievementKeys)) {
                continue;
            }

            // Check if condition is met
            if ($purchaseCount >= $achievement['condition']) {
                $this->unlockAchievement($user, $achievement);
                $newlyUnlocked[] = $achievement;

                Log::info('Achievement unlocked', [
                    'user_id' => $user->id,
                    'achievement' => $achievement['name'],
                    'purchase_count' => $purchaseCount,
                ]);
            }
        }

        // Check for badge upgrade
        if (count($newlyUnlocked) > 0) {
            $this->checkBadgeUpgrade($user, $previousBadge);
        }

        return $newlyUnlocked;
    }

    /**
     * Unlock an achievement for a user.
     */
    protected function unlockAchievement(User $user, array $achievement): void
    {
        // Find or create achievement record
        $achievementModel = Achievement::firstOrCreate(
            ['key' => $achievement['key']],
            [
                'name' => $achievement['name'],
                'description' => $achievement['description'],
                'icon' => $achievement['icon'],
                'condition_type' => 'purchase_count',
                'condition_value' => $achievement['condition'],
            ]
        );

        // Attach to user
        $user->achievements()->attach($achievementModel->id, [
            'unlocked_at' => now(),
        ]);

        // Fire event
        event(new AchievementUnlocked(
            user: $user,
            achievementKey: $achievement['key'],
            achievementName: $achievement['name'],
            achievementIcon: $achievement['icon']
        ));
    }

    /**
     * Check if user qualifies for a badge upgrade.
     */
    protected function checkBadgeUpgrade(User $user, array $previousBadge): void
    {
        $currentBadge = $this->getCurrentBadge($user);

        // If badge changed, fire event
        if ($currentBadge['key'] !== $previousBadge['key']) {
            event(new BadgeUnlocked(
                user: $user,
                badgeKey: $currentBadge['key'],
                badgeName: $currentBadge['name'],
                badgeIcon: $currentBadge['icon'],
                badgeColor: $currentBadge['color']
            ));

            Log::info('Badge unlocked', [
                'user_id' => $user->id,
                'previous_badge' => $previousBadge['name'],
                'new_badge' => $currentBadge['name'],
            ]);
        }
    }

    /**
     * Get user's current badge based on achievement count.
     */
    public function getCurrentBadge(User $user): array
    {
        $achievementCount = $user->achievements()->count();

        return $this->badges
            ->filter(fn($badge) => $achievementCount >= $badge['achievements_required'])
            ->last() ?? $this->badges->first();
    }

    /**
     * Get the next badge the user can unlock.
     */
    public function getNextBadge(User $user): ?array
    {
        $currentBadge = $this->getCurrentBadge($user);
        $currentIndex = $this->badges->search(fn($b) => $b['key'] === $currentBadge['key']);

        return $this->badges->get($currentIndex + 1);
    }

    /**
     * Get all unlocked achievements for a user.
     */
    public function getUnlockedAchievements(User $user): Collection
    {
        return $user->achievements()->get()->map(fn($a) => [
            'key' => $a->key,
            'name' => $a->name,
            'description' => $a->description,
            'icon' => $a->icon,
            'unlocked_at' => $a->pivot->unlocked_at,
        ]);
    }

    /**
     * Get next available achievements the user can unlock.
     */
    public function getNextAvailableAchievements(User $user): Collection
    {
        $unlockedKeys = $user->achievements()->pluck('key')->toArray();
        $purchaseCount = $user->purchases()->count();

        return $this->purchaseAchievements
            ->filter(fn($a) => !in_array($a['key'], $unlockedKeys))
            ->sortBy('condition')
            ->take(3)
            ->values();
    }

    /**
     * Get all achievements with their lock status for a user.
     */
    public function getAllAchievementsWithStatus(User $user): Collection
    {
        $unlockedKeys = $user->achievements()->pluck('key')->toArray();

        return $this->purchaseAchievements->map(fn($achievement) => [
            ...$achievement,
            'unlocked' => in_array($achievement['key'], $unlockedKeys),
        ]);
    }

    /**
     * Calculate remaining achievements needed for next badge.
     */
    public function getRemainingForNextBadge(User $user): int
    {
        $nextBadge = $this->getNextBadge($user);

        if (!$nextBadge) {
            return 0;
        }

        $currentCount = $user->achievements()->count();
        return max(0, $nextBadge['achievements_required'] - $currentCount);
    }

    /**
     * Calculate progress percentage towards next badge.
     */
    public function getProgressPercentage(User $user): int
    {
        $currentBadge = $this->getCurrentBadge($user);
        $nextBadge = $this->getNextBadge($user);

        if (!$nextBadge) {
            return 100; // Already at max badge
        }

        $achievementCount = $user->achievements()->count();
        $currentRequired = $currentBadge['achievements_required'];
        $nextRequired = $nextBadge['achievements_required'];

        $progress = $achievementCount - $currentRequired;
        $total = $nextRequired - $currentRequired;

        return min(100, (int) (($progress / $total) * 100));
    }
}
