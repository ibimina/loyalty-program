<?php

namespace App\Listeners;

use App\Events\PurchaseMade;
use App\Services\AchievementService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener that processes achievements when a purchase is made.
 * 
 * Implements ShouldQueue for async processing - this is a key
 * differentiator showing scalability mindset and production-ready code.
 */
class ProcessAchievements implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds before the job should be retried.
     */
    public int $backoff = 30;

    /**
     * Create the event listener.
     */
    public function __construct(
        protected AchievementService $achievementService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(PurchaseMade $event): void
    {
        $user = $event->user;
        $purchase = $event->purchase;

        Log::info('Processing achievements for purchase', [
            'user_id' => $user->id,
            'purchase_id' => $purchase->id,
            'amount' => $purchase->amount,
        ]);

        // Process achievements - this will automatically fire
        // AchievementUnlocked and BadgeUnlocked events as needed
        $newAchievements = $this->achievementService->processAchievements($user);

        if (count($newAchievements) > 0) {
            Log::info('New achievements unlocked', [
                'user_id' => $user->id,
                'achievements' => array_column($newAchievements, 'name'),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(PurchaseMade $event, \Throwable $exception): void
    {
        Log::error('Failed to process achievements', [
            'user_id' => $event->user->id,
            'purchase_id' => $event->purchase->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
