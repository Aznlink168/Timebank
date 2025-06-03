<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // The ID of the user this notification is for
        'type',    // Type of notification (e.g., 'new_assignment', 'status_update')
        'message', // The main notification message content
        'data',    // JSON field to store related data (e.g., service_request_id, assignment_id)
        'read_at', // Timestamp when the user read the notification
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'json',        // Casts 'data' to/from JSON
        'read_at' => 'datetime', // Casts 'read_at' to a Carbon datetime object
    ];

    /**
     * Get the user that this notification belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
