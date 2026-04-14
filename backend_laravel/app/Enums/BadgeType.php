<?php

namespace App\Enums;

/**
 * Badge tier types.
 * Using PHP 8.1+ backed enums for type safety.
 */
enum BadgeType: string
{
    case BEGINNER = 'beginner';
    case BRONZE = 'bronze';
    case SILVER = 'silver';
    case GOLD = 'gold';
    case PLATINUM = 'platinum';

    /**
     * Get the display name.
     */
    public function label(): string
    {
        return match($this) {
            self::BEGINNER => 'Beginner',
            self::BRONZE => 'Bronze',
            self::SILVER => 'Silver',
            self::GOLD => 'Gold',
            self::PLATINUM => 'Platinum',
        };
    }

    /**
     * Get the badge icon.
     */
    public function icon(): string
    {
        return match($this) {
            self::BEGINNER => '🌱',
            self::BRONZE => '🥉',
            self::SILVER => '🥈',
            self::GOLD => '🥇',
            self::PLATINUM => '💎',
        };
    }

    /**
     * Get the badge color.
     */
    public function color(): string
    {
        return match($this) {
            self::BEGINNER => '#8B4513',
            self::BRONZE => '#CD7F32',
            self::SILVER => '#C0C0C0',
            self::GOLD => '#FFD700',
            self::PLATINUM => '#E5E4E2',
        };
    }

    /**
     * Get achievements required for this badge.
     */
    public function achievementsRequired(): int
    {
        return match($this) {
            self::BEGINNER => 0,
            self::BRONZE => 1,
            self::SILVER => 3,
            self::GOLD => 5,
            self::PLATINUM => 6,
        };
    }

    /**
     * Get the next badge tier.
     */
    public function next(): ?self
    {
        return match($this) {
            self::BEGINNER => self::BRONZE,
            self::BRONZE => self::SILVER,
            self::SILVER => self::GOLD,
            self::GOLD => self::PLATINUM,
            self::PLATINUM => null,
        };
    }
}
