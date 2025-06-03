<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Mail\NewAssignmentNotification; // Assuming this will be created
use App\Mail\AssignmentStatusUpdateNotification; // Assuming this will be created
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Notify a user via their preferred channels and log the notification.
     *
     * @param User $user The user to notify.
     * @param string $type A string identifying the type of notification (e.g., 'new_assignment', 'status_update').
     * @param string $subject The subject line for emails, also used as a base for the persisted DB notification message.
     * @param \Illuminate\Mail\Mailable|null $mailableInstance A specific Mailable instance to send (e.g., NewAssignmentNotification).
     * @param string|null $smsMessage The message content for SMS, if applicable.
     * @param array $data Additional contextual data to store with the notification record (e.g., related model IDs like service_request_id).
     */
    public function notifyUser(
        User $user,
        string $type,
        string $subject,
        $mailableInstance = null, // Should be \Illuminate\Mail\Mailable or null
        ?string $smsMessage = null,
        array $data = []
    ) {
        // Determine user's notification preference, defaulting to 'email'.
        // Assumes 'notification_preference' field exists on User model (e.g., 'email', 'sms', 'both', 'none').
        // Assumes 'phone_number' field exists on User model for SMS.
        $preference = $user->notification_preference ?? 'email';

        // Persist the notification to the database regardless of delivery channels.
        // This provides an in-app notification history.
        Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'message' => $subject, // Store the subject or a summary
            'data' => $data,
        ]);

        // Send Email
        if (($preference === 'email' || $preference === 'both') && $mailableInstance) {
            try {
                Mail::to($user)->send($mailableInstance);
                Log::info("Email notification '{$type}' queued for user {$user->id}.");
            } catch (\Exception $e) {
                Log::error("Failed to send email notification '{$type}' to user {$user->id}: " . $e->getMessage());
            }
        } elseif (($preference === 'email' || $preference === 'both')) {
            // Fallback for simple messages if no specific Mailable is passed (optional).
            // Current implementation relies on specific Mailables being passed.
            Log::warning("Email notification '{$type}' for user {$user->id} requested, but no Mailable instance was provided.");
        }


        // Send SMS if preference allows, an SMS message is provided, and the user has a phone number.
        if (($preference === 'sms' || $preference === 'both') && !empty($smsMessage) && !empty($user->phone_number)) {
            if ($this->smsService->sendMessage($user->phone_number, $smsMessage)) {
                Log::info("SMS notification '{$type}' attempt recorded for user {$user->id}. Check SmsService logs for delivery status.");
            } else {
                // SmsService sendMessage method already logs errors/warnings.
                Log::warning("SMS notification '{$type}' for user {$user->id} (phone: {$user->phone_number}) could not be initiated by NotificationService (see SmsService logs).");
            }
        } elseif (($preference === 'sms' || $preference === 'both') && empty($user->phone_number)) {
            Log::warning("SMS notification '{$type}' for user {$user->id} requested, but user has no phone number.");
        } elseif (($preference === 'sms' || $preference === 'both') && empty($smsMessage) ) {
             Log::warning("SMS notification '{$type}' for user {$user->id} requested, but no SMS message content was provided.");
        }
    }
}
