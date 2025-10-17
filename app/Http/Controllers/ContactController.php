<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
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

    Mail::to('lotannaemmanuelotikpo@gmail.com')->send(new ContactMail($details));

    return response('success', 200);
}

}
