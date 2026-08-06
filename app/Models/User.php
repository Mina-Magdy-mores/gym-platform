<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Payment\Models\Payment;
use Modules\Subscription\Models\Booking;
use Modules\Subscription\Models\UserSubscription;
use Modules\Wallet\Models\TrainerWallet;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens, InteractsWithMedia;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Register media collections for avatar and certificates.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile();
        $this->addMediaCollection('certificates');
    }

    /**
     * Register media conversions for automatic image thumbnail creation.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10);
    }

    /**
     * Relationship: User's subscriptions history.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Relationship: User's current active subscription.
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class)
            ->where('status', 'active')
            ->where('ends_at', '>=', now())
            ->latestOfMany();
    }

    /**
     * Relationship: User's queued future subscription.
     */
    public function queuedSubscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class)
            ->where('status', 'queued')
            ->latestOfMany();
    }

    /**
     * Relationship: Member's booked trainer sessions.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    /**
     * Relationship: Trainer's upcoming booked sessions by members.
     */
    public function trainerBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'trainer_id');
    }

    /**
     * Relationship: User's payment transaction history.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Relationship: Trainer's financial wallet.
     */
    public function trainerWallet(): HasOne
    {
        return $this->hasOne(TrainerWallet::class);
    }
}
