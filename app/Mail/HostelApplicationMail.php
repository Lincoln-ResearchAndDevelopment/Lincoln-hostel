<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HostelApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $applicationData;

    public function __construct($applicationData)
    {
        $this->applicationData = $applicationData;
    }

    public function build()
    {
        return $this->view('emails.hostel_application')
                    ->with([
                        'applicationData' => $this->applicationData,
                    ]);
    }
}

