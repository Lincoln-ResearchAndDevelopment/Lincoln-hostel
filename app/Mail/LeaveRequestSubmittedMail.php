<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveRequestSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveRequest;
    public $recipientType; // 'admin' or 'parent'

    public function __construct(LeaveRequest $leaveRequest, string $recipientType = 'admin')
    {
        $this->leaveRequest = $leaveRequest;
        $this->recipientType = $recipientType;
    }

    public function envelope(): Envelope
    {
        $subject = $this->recipientType === 'admin' 
            ? 'New Leave Request - ' . $this->leaveRequest->student->full_name
            : 'Leave Request Submitted - ' . $this->leaveRequest->student->full_name;

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.leave_request_submitted',
            with: [
                'leaveRequest' => $this->leaveRequest,
                'student' => $this->leaveRequest->student,
                'recipientType' => $this->recipientType,
            ]
        );
    }
}
