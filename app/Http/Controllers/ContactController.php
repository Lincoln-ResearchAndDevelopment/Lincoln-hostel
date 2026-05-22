<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;

use App\Traits\TracksEmails;

class ContactController extends Controller
{
    use TracksEmails;

    public function send(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $details = [
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ];

        $recipient = config('mail.from.address') ?: 'lincolnuninigeria@gmail.com';
        $this->sendTrackedEmail('contact_form', $recipient, new ContactMail($details), ['sender_email' => $validated['email'], 'sender_name' => $validated['name']]);

        return response('success', 200);
    }
}
