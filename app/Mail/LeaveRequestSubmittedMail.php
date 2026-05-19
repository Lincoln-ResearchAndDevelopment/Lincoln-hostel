<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LeaveRequestSubmittedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $leaveRequest;
    public $recipientType; // 'admin', 'parent', or 'student'

    public function __construct(LeaveRequest $leaveRequest, string $recipientType = 'admin')
    {
        $this->leaveRequest = $leaveRequest;
        $this->recipientType = $recipientType;
    }

    public function envelope(): Envelope
    {
        $subject = $this->recipientType === 'admin' 
            ? 'New Leave Request - ' . $this->leaveRequest->student->full_name
            : ($this->recipientType === 'student'
                ? 'Your Leave Request Submitted'
                : 'Leave Request Submitted - ' . $this->leaveRequest->student->full_name);

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.students.leave_submitted',
            with: [
                'leaveRequest' => $this->leaveRequest,
                'student' => $this->leaveRequest->student,
                'recipientType' => $this->recipientType,
            ]
        );
    }
}
