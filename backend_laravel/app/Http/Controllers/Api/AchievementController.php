<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AchievementService;
use Illuminate\Http\JsonResponse;

/**
 * API Controller for user achievements and badges.
 */
class AchievementController extends Controller
{
    public function __construct(
        protected AchievementService $achievementService
    ) {
    }

    /**
     * Get user's achievements and badge information.
     * 
     * @param User $user
     * @return JsonResponse
     * 
     * @response {
     *   "success": true,
     *   "data": {
     *     "user": { "id": 1, "name": "John Doe", "email": "john@example.com" },
     *     "unlocked_achievements": ["First Purchase", "Returning Customer"],
     *     "next_available_achievements": ["Loyal Shopper"],
     *     "current_badge": "Bronze",
     *     "next_badge": "Silver",
     *     "remaining_to_unlock_next_badge": 2,
     *     "progress_percentage": 33,
     *     "all_achievements": [...],
     *     "all_badges": [...],
     *     "stats": { "total_purchases": 3, "total_achievements": 2 }
     *   }
     * }
     */
    public function show(User $user): JsonResponse
    {
        // Get achievement data
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);
        $nextAvailableAchievements = $this->achievementService->getNextAvailableAchievements($user);
        $allAchievementsWithStatus = $this->achievementService->getAllAchievementsWithStatus($user);

        // Get badge data
        $currentBadge = $this->achievementService->getCurrentBadge($user);
        $nextBadge = $this->achievementService->getNextBadge($user);
        $remainingForNextBadge = $this->achievementService->getRemainingForNextBadge($user);
        $progressPercentage = $this->achievementService->getProgressPercentage($user);

        // Get all badges with current status
        $allBadges = collect(config('achievements.badges'))->map(function ($badge) use ($currentBadge) {
            return [
                ...$badge,
                'is_current' => $badge['key'] === $currentBadge['key'],
                'is_unlocked' => $badge['achievements_required'] <= $currentBadge['achievements_required'],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],

                // Required fields from assessment
                'unlocked_achievements' => $unlockedAchievements->pluck('name')->toArray(),
                'next_available_achievements' => $nextAvailableAchievements->pluck('name')->toArray(),
                'current_badge' => $currentBadge['name'],
                'next_badge' => $nextBadge ? $nextBadge['name'] : null,
                'remaining_to_unlock_next_badge' => $remainingForNextBadge,

                // Extra value-add fields (differentiator!)
                'progress_percentage' => $progressPercentage,

                // Detailed data for rich UI
                'achievements' => [
                    'unlocked' => $unlockedAchievements->toArray(),
                    'next_available' => $nextAvailableAchievements->toArray(),
                    'all' => $allAchievementsWithStatus->toArray(),
                ],

                'badges' => [
                    'current' => $currentBadge,
                    'next' => $nextBadge,
                    'all' => $allBadges->toArray(),
                ],

                // Stats for dashboard
                'stats' => [
                    'total_purchases' => $user->purchases()->count(),
                    'total_achievements_unlocked' => $unlockedAchievements->count(),
                    'total_achievements_available' => count(config('achievements.purchase_achievements')),
                    'total_badges' => count(config('achievements.badges')),
                    'total_cashback_earned' => $user->total_cashback_earned,
                ],
            ],
        ]);
    }

    /**
     * Get achievement history timeline for a user.
     * 
     * @param User $user
     * @return JsonResponse
     */
    public function history(User $user): JsonResponse
    {
        $history = $user->achievementHistory()
            ->with('achievement')
            ->get()
            ->map(fn($record) => [
                'achievement' => [
                    'key' => $record->achievement->key,
                    'name' => $record->achievement->name,
                    'icon' => $record->achievement->icon,
                ],
                'unlocked_at' => $record->unlocked_at->toIso8601String(),
                'unlocked_at_human' => $record->unlocked_at->diffForHumans(),
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'history' => $history,
                'count' => $history->count(),
            ],
        ]);
    }
}
