<?php

namespace App\Livewire;

use App\Models\ServiceAssignment;
use App\Models\User; // Added
use App\Services\NotificationService; // Added
use App\Mail\AssignmentStatusUpdateNotification; // Added
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class QrScannerPage extends Component
{
    public $scan_result = '';       // Holds the raw string from the QR code scanner
    public $error_message = '';     // Error message to display to the user
    public $success_message = '';   // Success message to display to the user

    /**
     * Listeners for events emitted from JavaScript or other components.
     * 'processQrScanned' event (emitted from JS after QR scan) calls the 'processQrCode' method.
     */
    protected $listeners = ['processQrScanned' => 'processQrCode'];

    /**
     * Processes the scanned QR code token.
     * Finds the corresponding ServiceAssignment.
     * Updates the assignment status (accepted -> in_progress, or in_progress -> completed).
     * Dispatches notifications to relevant users.
     *
     * @param string $qrCodeToken The token string obtained from the QR code.
     * @param NotificationService $notificationService Injected service for sending notifications.
     */
    public function processQrCode(string $qrCodeToken, NotificationService $notificationService)
    {
        $this->scan_result = $qrCodeToken; // Store the raw scan result for potential display
        $this->error_message = '';         // Reset messages
        $this->success_message = '';
        Log::info("QR Code Scanned: " . $qrCodeToken);

        if (empty($qrCodeToken)) {
            $this->error_message = 'Scanned QR code token is empty.';
            $this->dispatch('scanProcessed', $this->error_message); // Dispatch event for JS feedback if needed
            return;
        }

        // Find the assignment, eager loading related models for notifications
        $assignment = ServiceAssignment::with(['serviceRequest.requester', 'volunteer'])
                        ->where('qr_code', $qrCodeToken)
                        ->first();

        if ($assignment) {
            $originalStatus = $assignment->status; // For logging and messaging
            $newStatus = '';
            $notificationSubject = '';
            $notificationEmailMessage = '';
            $notificationSmsMessage = '';

            if ($assignment->status === 'accepted') {
                $assignment->status = 'in_progress';
                $assignment->started_at = now();
                $newStatus = 'In Progress';
                $notificationSubject = "Assignment Started: {$assignment->serviceRequest->title}";
                $notificationEmailMessage = "The service assignment '{$assignment->serviceRequest->title}' has been marked as In Progress by the volunteer.";
                $notificationSmsMessage = "Service '{$assignment->serviceRequest->title}' is now In Progress.";
            } elseif ($assignment->status === 'in_progress') {
                $assignment->status = 'completed';
                $assignment->completed_at = now();
                $newStatus = 'Completed';
                $notificationSubject = "Assignment Completed: {$assignment->serviceRequest->title}";
                $notificationEmailMessage = "The service assignment '{$assignment->serviceRequest->title}' has been marked as Completed by the volunteer.";
                $notificationSmsMessage = "Service '{$assignment->serviceRequest->title}' is now Completed.";
            } else {
                $this->error_message = "Service assignment status ('{$originalStatus}') cannot be updated via QR scan at this time.";
                $this->dispatch('scanProcessed', $this->error_message);
                return;
            }

            $assignment->save();
            $this->success_message = "Service assignment (ID: {$assignment->id}) status updated from '{$originalStatus}' to '{$newStatus}'.";
            Log::info($this->success_message);
            $this->dispatch('scanProcessed', $this->success_message);

            // Notify Requester
            if ($assignment->serviceRequest && $assignment->serviceRequest->requester) {
                $mailableToRequester = new AssignmentStatusUpdateNotification($assignment, $assignment->serviceRequest->requester, $notificationEmailMessage, $notificationSubject);
                $notificationService->notifyUser(
                    $assignment->serviceRequest->requester,
                    'assignment_status_update',
                    $notificationSubject,
                    $mailableToRequester,
                    $notificationSmsMessage,
                    ['service_request_id' => $assignment->service_request_id, 'assignment_id' => $assignment->id]
                );
            }
            // Notify Volunteer (optional, as they triggered it, but good for confirmation)
            if ($assignment->volunteer) {
                 $volunteerMessage = "You have successfully updated the assignment '{$assignment->serviceRequest->title}' to {$newStatus}.";
                 $mailableToVolunteer = new AssignmentStatusUpdateNotification($assignment, $assignment->volunteer, $volunteerMessage, $notificationSubject);
                 $notificationService->notifyUser(
                    $assignment->volunteer,
                    'assignment_status_self_update',
                    $notificationSubject,
                    $mailableToVolunteer,
                    "You updated service '{$assignment->serviceRequest->title}' to {$newStatus}.",
                    ['service_request_id' => $assignment->service_request_id, 'assignment_id' => $assignment->id]
                );
            }

        } else {
            $this->error_message = 'No matching service assignment found for this QR code.';
            Log::error($this->error_message . " Token: " . $qrCodeToken); // Corrected Log_error to Log::error
            $this->dispatch('scanProcessed', $this->error_message);
        }
    }

    public function render()
    {
        return view('livewire.qr-scanner-page');
    }
}
