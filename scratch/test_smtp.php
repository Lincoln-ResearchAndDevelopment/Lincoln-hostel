<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\Mail::raw('This is a test email from LincHostel', function ($message) {
        $message->to('chimezietchris@gmail.com')
                ->subject('SMTP Test');
    });
    echo "Mail sent successfully.\n";
} catch (\Exception $e) {
    echo "Mail failed: " . $e->getMessage() . "\n";
}
