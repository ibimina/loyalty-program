<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get all purchases for the user.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Get cashback payment records for the user.
     */
    public function cashbackPayments(): HasMany
    {
        return $this->hasMany(CashbackPayment::class);
    }

    /**
     * Get all unlocked achievements for the user.
     */
    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    /**
     * Get achievement history for the user.
     */
    public function achievementHistory(): HasMany
    {
        return $this->hasMany(UserAchievement::class)->orderBy('unlocked_at', 'desc');
    }

    /**
     * Get the user's current badge based on achievements count.
     */
    public function getCurrentBadgeAttribute(): array
    {
        $achievementCount = $this->achievements()->count();
        $badges = collect(config('achievements.badges'));

        return $badges->filter(function ($badge) use ($achievementCount) {
            return $achievementCount >= $badge['achievements_required'];
        })->last() ?? $badges->first();
    }

    /**
     * Get the user's total purchase count.
     */
    public function getPurchaseCountAttribute(): int
    {
        return $this->purchases()->count();
    }

    /**
     * Get total successful cashback earned by the user.
     */
    public function getTotalCashbackEarnedAttribute(): int
    {
        return (int) $this->cashbackPayments()
            ->where('status', 'completed')
            ->sum('amount');
    }
}
