<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'requester_id',        // Foreign key for the User who created the request
        'service_category_id', // Foreign key for the ServiceCategory
        'title',               // Title of the service request
        'description',         // Detailed description of the service needed
        'status',              // Current status (e.g., pending, assigned, in_progress, completed, cancelled)
        'location',            // Optional location for the service
        'required_skills',     // JSON array of skill IDs or skill names needed for the service
        'urgency',             // Urgency level (e.g., low, medium, high)
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'required_skills' => 'json', // Casts the required_skills attribute to/from JSON
    ];

    /**
     * Get the user who created the service request.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Get the category of this service request.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    /**
     * Get all assignments for this service request.
     * A service request can have multiple assignments if, for example, one is declined and then it's reassigned.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(ServiceAssignment::class);
    }

    /**
     * Get the history of priority changes for this service request.
     */
    public function priorityChanges(): HasMany
    {
        return $this->hasMany(PriorityChange::class);
    }

    /**
     * Get all watchlist entries for this service request (users watching this request).
     */
    public function watchlistedByUsers(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }
}
