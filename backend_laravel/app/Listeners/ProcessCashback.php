<?php

namespace App\Listeners;

use App\Events\BadgeUnlocked;
use App\Services\PaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener that processes cashback payment when a badge is unlocked.
 * 
 * Implements ShouldQueue for async processing - important for payment
 * operations to not block the main request.
 */
class ProcessCashback implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 5;

    /**
     * The number of seconds before the job should be retried.
     */
    public int $backoff = 60;

    /**
     * Create the event listener.
     */
    public function __construct(
        protected PaymentService $paymentService
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(BadgeUnlocked $event): void
    {
        $user = $event->user;
        $cashbackAmount = config('achievements.cashback.default_amount', 300);

        Log::info('Processing cashback for badge unlock', [
            'user_id' => $user->id,
            'badge' => $event->badgeName,
            'amount' => $cashbackAmount,
        ]);

        // Send cashback payment
        $result = $this->paymentService->sendCashback(
            user: $user,
            amount: $cashbackAmount,
            reason: "Badge Unlock Reward: {$event->badgeName}",
            context: [
                'badge_key' => $event->badgeKey,
                'badge_name' => $event->badgeName,
            ]
        );

        if ($result['success']) {
            Log::info('Cashback sent successfully', [
                'user_id' => $user->id,
                'transaction_id' => $result['transaction_id'],
                'amount' => $result['formatted_amount'],
            ]);
        } else {
            Log::warning('Cashback payment failed', [
                'user_id' => $user->id,
                'error' => $result['message'],
            ]);

            // Could implement retry logic or notification here
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(BadgeUnlocked $event, \Throwable $exception): void
    {
        Log::error('Failed to process cashback', [
            'user_id' => $event->user->id,
            'badge' => $event->badgeName,
            'error' => $exception->getMessage(),
        ]);

        // In production: notify admin, queue for manual review, etc.
    }
}
