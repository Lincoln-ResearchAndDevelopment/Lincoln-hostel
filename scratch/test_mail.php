<?php

// Load Laravel Bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

$toEmail = 'chimezietchris@gmail.com';
echo "Attempting to send test email to: $toEmail\n";

try {
    Mail::raw('This is a test email from the Lincoln Hostel management system to verify SMTP and Email functionality.', function ($message) use ($toEmail) {
        $message->to($toEmail)
                ->subject('Lincoln Hostel SMTP Connection Test');
    });
    echo "SUCCESS: Test email has been successfully sent to $toEmail!\n";
} catch (\Exception $e) {
    echo "ERROR: Failed to send email.\n";
    echo "Exception Class: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
