<?php

namespace App\Mail;

use App\Models\User;
use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewAssignmentNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $volunteer;
    public ServiceRequest $serviceRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(User $volunteer, ServiceRequest $serviceRequest)
    {
        $this->volunteer = $volunteer;
        $this->serviceRequest = $serviceRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Service Request Assignment: ' . $this->serviceRequest->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.notifications.new-assignment',
            with: [
                'volunteerName' => $this->volunteer->name,
                'requestTitle' => $this->serviceRequest->title,
                'requestDescription' => $this->serviceRequest->description,
                'requestUrl' => route('service-requests.show', $this->serviceRequest),
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
