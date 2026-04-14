<?php

namespace App\Events;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a user unlocks an achievement.
 * Implements ShouldBroadcast for real-time updates (optional enhancement).
 */
class AchievementUnlocked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly string $achievementKey,
        public readonly string $achievementName,
        public readonly string $achievementIcon
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
            'achievement_key' => $this->achievementKey,
            'achievement_name' => $this->achievementName,
            'achievement_icon' => $this->achievementIcon,
            'message' => "🎉 Achievement Unlocked: {$this->achievementName}!",
        ];
    }
}
