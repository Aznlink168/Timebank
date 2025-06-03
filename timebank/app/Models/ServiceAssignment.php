<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\QrCodeService; // Added import

class ServiceAssignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_request_id', // Foreign key for the ServiceRequest
        'volunteer_id',       // Foreign key for the User (volunteer)
        'assigned_at',        // Timestamp when the assignment was made/accepted
        'started_at',         // Timestamp when work started (e.g., first QR scan)
        'completed_at',       // Timestamp when work was completed (e.g., second QR scan)
        'status',             // Status of the assignment (e.g., pending_acceptance, accepted, declined, in_progress, completed, cancelled)
        'qr_code',            // Unique token for QR code scanning
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the service request associated with this assignment.
     */
    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /**
     * Get the volunteer assigned to this service request.
     */
    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'volunteer_id');
    }

    /**
     * Generate and store a QR code for this assignment.
     * Returns the SVG string of the QR code.
     */
    public function generateQrCode(): string
    {
        $qrCodeService = app(QrCodeService::class);
        return $qrCodeService->generateForAssignment($this);
    }

    /**
     * Accessor for the QR code as a data URL.
     * Example: <img src="{{ $assignment->qr_code_data_url }}" alt="QR Code">
     *
     * @return string|null
     */
    public function getQrCodeDataUrlAttribute(): ?string
    {
        $qrCodeService = app(QrCodeService::class);
        return $qrCodeService->getQrCodeDataUrl($this);
    }

    /**
     * Ensures a QR code token exists for this assignment.
     * If not, it generates one.
     */
    public function ensureQrCodeTokenExists(): string
    {
        $qrCodeService = app(QrCodeService::class);
        return $qrCodeService->ensureQrCodeToken($this);
    }
}
