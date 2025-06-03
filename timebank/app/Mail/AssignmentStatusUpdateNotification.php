<?php

namespace App\Mail;

use App\Models\User;
use App\Models\ServiceAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssignmentStatusUpdateNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ServiceAssignment $assignment;
    public User $recipient;
    public string $customMessage; // Renamed from 'message' to avoid conflict with Mailable's own message property
    public string $subjectLine;

    /**
     * Create a new message instance.
     */
    public function __construct(ServiceAssignment $assignment, User $recipient, string $customMessage, string $subjectLine)
    {
        $this->assignment = $assignment;
        $this->recipient = $recipient;
        $this->customMessage = $customMessage;
        $this->subjectLine = $subjectLine;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.notifications.assignment-status-update',
            with: [
                'recipientName' => $this->recipient->name,
                'emailMessage' => $this->customMessage, // Pass the custom message to the view
                'assignment' => $this->assignment,
                'serviceRequest' => $this->assignment->serviceRequest, // For convenience in the email
                'requestUrl' => route('service-requests.show', $this->assignment->serviceRequest),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
