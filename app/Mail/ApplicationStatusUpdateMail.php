<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\HostelApplication;

class ApplicationStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    public function __construct(HostelApplication $application)
    {
        $this->application = $application;
    }

    public function build()
    {
        return $this->subject('Hostel Application Status Update - ' . $this->application->application_number)
                    ->view('emails.application_status_update')
                    ->with([
                        'application' => $this->application,
                    ]);
    }
}
