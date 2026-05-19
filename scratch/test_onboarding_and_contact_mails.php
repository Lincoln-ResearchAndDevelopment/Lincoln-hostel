<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\Mail\StudentOnboardingMail;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;

echo "=== EMAIL RENDER AND DISPATCH TEST ===\n";

try {
    // 1. Find the test student
    $student = Student::find(110);
    if (!$student) {
        throw new \Exception("Test student with ID 110 not found!");
    }

    echo "Found student: {$student->full_name} (Email: {$student->email})\n";

    // 2. Send StudentOnboardingMail to the student
    echo "Sending StudentOnboardingMail...\n";
    Mail::to($student->email)->send(new StudentOnboardingMail($student));
    echo "SUCCESS: Onboarding email sent!\n\n";

    // 3. Send ContactMail to the standard system address
    $recipient = config('mail.from.address') ?: 'lincolnuninigeria@gmail.com';
    echo "Sending ContactMail to: $recipient...\n";
    
    $details = [
        'name'    => 'Test System User',
        'email'   => 'testsender@example.com',
        'subject' => 'Professional Contact Form Test',
        'message' => 'Hello Lincoln Hostel, this is a premium markdown contact form notification testing real system integration. Every layer and flow is complete and robust!',
    ];
    Mail::to($recipient)->send(new ContactMail($details));
    echo "SUCCESS: Contact email sent!\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
}
