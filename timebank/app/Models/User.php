<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'language_preference', // User's preferred language e.g., 'en', 'es'
        'availability_details', // Text field for general availability notes
        'phone_number', // User's phone number, potentially for SMS notifications
        'notification_preference', // User's preferred notification method e.g., 'email', 'sms', 'both'
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    /**
     * The skills that the user possesses.
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }

    /**
     * Service requests created by the user.
     */
    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'requester_id');
    }

    /**
     * Service assignments where this user is the volunteer.
     */
    public function assignedServices(): HasMany
    {
        return $this->hasMany(ServiceAssignment::class, 'volunteer_id');
    }

    /**
     * Regular availability slots for the user.
     */
    public function availability(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    /**
     * Specific date/time exceptions to the user's regular availability.
     */
    public function availabilityExceptions(): HasMany
    {
        return $this->hasMany(AvailabilityException::class);
    }

    /**
     * Notifications for the user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Service requests the user has added to their watchlist.
     */
    public function watchlistItems(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    /**
     * Priority changes made by this user (typically an admin).
     */
    public function priorityChangesMade(): HasMany
    {
        return $this->hasMany(PriorityChange::class, 'changed_by_user_id');
    }
}
