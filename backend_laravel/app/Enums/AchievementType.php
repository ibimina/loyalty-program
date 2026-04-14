<?php

namespace App\Enums;

/**
 * Achievement types for categorizing achievements.
 * Using PHP 8.1+ backed enums for type safety.
 */
enum AchievementType: string
{
    case PURCHASE_COUNT = 'purchase_count';
    case TOTAL_SPENT = 'total_spent';
    case STREAK = 'streak';
    case SPECIAL = 'special';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match($this) {
            self::PURCHASE_COUNT => 'Purchase Count',
            self::TOTAL_SPENT => 'Total Spent',
            self::STREAK => 'Purchase Streak',
            self::SPECIAL => 'Special Achievement',
        };
    }

    /**
     * Get description.
     */
    public function description(): string
    {
        return match($this) {
            self::PURCHASE_COUNT => 'Based on number of purchases made',
            self::TOTAL_SPENT => 'Based on total amount spent',
            self::STREAK => 'Based on consecutive purchase days',
            self::SPECIAL => 'Special promotional achievements',
        };
    }
}
