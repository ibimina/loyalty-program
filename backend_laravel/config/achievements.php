<?php

/**
 * Achievement & Badge Configuration
 * 
 * This config-driven approach allows easy modification of achievements
 * without touching core logic - a key differentiator showing scalability mindset.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Purchase-Based Achievements
    |--------------------------------------------------------------------------
    |
    | Achievements unlocked based on total purchase count.
    | Each achievement has a unique key, display name, description, and condition.
    |
    */
    'purchase_achievements' => [
        [
            'key' => 'first_purchase',
            'name' => 'First Purchase',
            'description' => 'Made your very first purchase!',
            'icon' => '🛒',
            'condition' => 1,
        ],
        [
            'key' => 'returning_customer',
            'name' => 'Returning Customer',
            'description' => 'Made 3 purchases',
            'icon' => '🔄',
            'condition' => 3,
        ],
        [
            'key' => 'loyal_shopper',
            'name' => 'Loyal Shopper',
            'description' => 'Made 5 purchases',
            'icon' => '💝',
            'condition' => 5,
        ],
        [
            'key' => 'shopaholic',
            'name' => 'Shopaholic',
            'description' => 'Made 10 purchases',
            'icon' => '🛍️',
            'condition' => 10,
        ],
        [
            'key' => 'power_buyer',
            'name' => 'Power Buyer',
            'description' => 'Made 25 purchases',
            'icon' => '⚡',
            'condition' => 25,
        ],
        [
            'key' => 'ultimate_shopper',
            'name' => 'Ultimate Shopper',
            'description' => 'Made 50 purchases',
            'icon' => '👑',
            'condition' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Badge Tiers
    |--------------------------------------------------------------------------
    |
    | Badges are unlocked based on achievement count.
    | Each badge tier triggers a cashback reward.
    |
    */
    'badges' => [
        [
            'key' => 'beginner',
            'name' => 'Beginner',
            'description' => 'Just getting started',
            'icon' => '🌱',
            'color' => '#6B7280',
            'achievements_required' => 0,
            'cashback_amount' => 0,
        ],
        [
            'key' => 'bronze',
            'name' => 'Bronze',
            'description' => 'Unlocked 1 achievement',
            'icon' => '🥉',
            'color' => '#CD7F32',
            'achievements_required' => 1,
            'cashback_amount' => 300,
        ],
        [
            'key' => 'silver',
            'name' => 'Silver',
            'description' => 'Unlocked 3 achievements',
            'icon' => '🥈',
            'color' => '#C0C0C0',
            'achievements_required' => 3,
            'cashback_amount' => 300,
        ],
        [
            'key' => 'gold',
            'name' => 'Gold',
            'description' => 'Unlocked 5 achievements',
            'icon' => '🥇',
            'color' => '#FFD700',
            'achievements_required' => 5,
            'cashback_amount' => 300,
        ],
        [
            'key' => 'platinum',
            'name' => 'Platinum',
            'description' => 'Unlocked all achievements',
            'icon' => '💎',
            'color' => '#E5E4E2',
            'achievements_required' => 6,
            'cashback_amount' => 300,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cashback Configuration
    |--------------------------------------------------------------------------
    */
    'cashback' => [
        'default_amount' => 300, // in Naira
        'currency' => 'NGN',
        'currency_symbol' => '₦',
    ],
];
