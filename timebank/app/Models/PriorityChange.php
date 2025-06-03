<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriorityChange extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_request_id', // Foreign key for the ServiceRequest whose priority changed
        'changed_by_user_id', // Foreign key for the User (typically admin) who made the change
        'old_urgency',        // The urgency level before the change
        'new_urgency',        // The urgency level after the change
        'reason',             // Optional reason for the priority change
    ];

    /**
     * Get the service request associated with this priority change.
     */
    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /**
     * Get the user who made this priority change.
     */
    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
