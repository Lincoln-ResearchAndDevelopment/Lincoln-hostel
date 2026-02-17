<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS to a phone number
     *
     * @param string $to Phone number
     * @param string $message Message content
     * @return bool
     */
    public function sendSms($to, $message)
    {
        // Remove leading 0 and add country code if needed (assuming Nigeria +234 for now based on context)
        // detailed formatting can be improved based on specific gateway requirements
        
        try {
            // Placeholder for SMS Gateway integration
            // You would typically replace this with a call to your SMS provider API
            // Example: Twilio, Termii, Infobip, etc.
            
            // Log the SMS for testing purposes
            Log::info("SMS SENT TO: {$to}");
            Log::info("MESSAGE: {$message}");
            
            // TODO: Uncomment and configure one of the examples below for real sending
            
            /*
            // Example for Termii (Popular in Nigeria)
            $response = Http::post('https://api.ng.termii.com/api/sms/send', [
                'to' => $to,
                'from' => 'LincHostel',
                'sms' => $message,
                'type' => 'plain',
                'channel' => 'generic',
                'api_key' => env('TERMII_API_KEY'),
            ]);
            
            return $response->successful();
            */

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send SMS: " . $e->getMessage());
            return false;
        }
    }
}
