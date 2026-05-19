<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\HostelApplication;

class HostelApplicationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $application;

    public function __construct(HostelApplication $application)
    {
        $this->application = $application;
    }

    public function build()
    {
        return $this->subject('Hostel Application Confirmation - ' . $this->application->application_number)
                    ->view('emails.hostel_application_confirmation')
                    ->with([
                        'application' => $this->application,
                    ]);
    }
}

