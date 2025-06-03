<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailabilityException extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string|boolean>
     */
    protected $fillable = [
        'user_id',          // Foreign key for the User
        'date',             // The specific date of the exception
        'start_time',       // Optional start time for the exception period
        'end_time',         // Optional end time for the exception period
        'is_unavailable',   // Boolean: true if user is unavailable, false if this record marks a specific available time
        'description',      // Optional reason for the exception
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',                   // Casts 'date' to a Carbon date object
        'is_unavailable' => 'boolean',      // Casts 'is_unavailable' to boolean
        'start_time' => 'datetime:H:i',     // Or 'H:i:s' if seconds are stored, nullable
        'end_time' => 'datetime:H:i',       // Or 'H:i:s', nullable
    ];

    /**
     * Get the user that this availability exception belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
