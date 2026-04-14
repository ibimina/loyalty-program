<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a user unlocks a new badge tier.
 * This event triggers the cashback payment.
 */
class BadgeUnlocked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly string $badgeKey,
        public readonly string $badgeName,
        public readonly string $badgeIcon,
        public readonly string $badgeColor
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('user.' . $this->user->id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'badge_key' => $this->badgeKey,
            'badge_name' => $this->badgeName,
            'badge_icon' => $this->badgeIcon,
            'badge_color' => $this->badgeColor,
            'message' => "🏆 New Badge Unlocked: {$this->badgeName}! You've earned ₦300 cashback!",
        ];
    }
}
