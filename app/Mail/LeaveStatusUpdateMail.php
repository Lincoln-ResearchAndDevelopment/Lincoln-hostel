<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveRequest;
    public $recipientType; // 'student' or 'parent'

    public function __construct(LeaveRequest $leaveRequest, string $recipientType = 'student')
    {
        $this->leaveRequest = $leaveRequest;
        $this->recipientType = $recipientType;
    }

    public function envelope(): Envelope
    {
        $status = ucfirst($this->leaveRequest->status);
        return new Envelope(
            subject: "Leave Request {$status} - LincHostel"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.leave_status_update',
            with: [
                'leaveRequest' => $this->leaveRequest,
                'student' => $this->leaveRequest->student,
                'recipientType' => $this->recipientType,
            ]
        );
    }
}
