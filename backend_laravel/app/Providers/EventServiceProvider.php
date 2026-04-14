<?php

namespace App\Providers;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Events\PurchaseMade;
use App\Listeners\ProcessAchievements;
use App\Listeners\ProcessCashback;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // Purchase triggers achievement processing
        PurchaseMade::class => [
            ProcessAchievements::class,
        ],
        
        // Badge unlock triggers cashback payment
        BadgeUnlocked::class => [
            ProcessCashback::class,
        ],
        
        // Achievement unlocked - could add more listeners here
        // e.g., SendAchievementNotification::class
        AchievementUnlocked::class => [
            // Add listeners here
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
