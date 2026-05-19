<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$testEmail = 'chimezietchris@gmail.com';

try {
    echo "Attempting to send test email to $testEmail...\n";
    Mail::raw('This is a test email from LincHostel system to verify SMTP configuration.', function ($message) use ($testEmail) {
        $message->to($testEmail)
                ->subject('SMTP Test Email');
    });
    echo "SUCCESS: Email sent (according to Laravel).\n";
} catch (\Exception $e) {
    echo "FAILURE: " . $e->getMessage() . "\n";
    Log::error("Manual SMTP Test Failed: " . $e->getMessage());
}
