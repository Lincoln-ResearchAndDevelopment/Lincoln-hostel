<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\HostelApplication;

class AdminApplicationNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    public function __construct(HostelApplication $application)
    {
        $this->application = $application;
    }

    public function build()
    {
        return $this->subject('New Hostel Application Received - ' . $this->application->application_number)
                    ->view('emails.admin_application_notification')
                    ->with([
                        'application' => $this->application,
                    ]);
    }
}