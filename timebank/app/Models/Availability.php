<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',     // Foreign key for the User
        'day_of_week', // Integer representing day of week (e.g., 0 for Sunday, 1 for Monday, etc.)
        'start_time',  // Start time of availability slot (e.g., "09:00:00")
        'end_time',    // End time of availability slot (e.g., "17:00:00")
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime:H:i', // Or 'H:i:s' if seconds are stored
        'end_time' => 'datetime:H:i',   // Or 'H:i:s'
    ];

    /**
     * Get the user that this availability slot belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
