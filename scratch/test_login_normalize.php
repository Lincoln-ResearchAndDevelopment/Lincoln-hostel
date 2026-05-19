<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// The student has: admission_number = LUC-NGA-002-ADM-8140374, contact_number = +234 803 834 1496
// Test that various input formats will match

$testInputs = [
    '+234 803 834 1496',    // exact match
    '08038341496',          // local format, no spaces  
    '0803 834 1496',        // local with spaces
    '+2348038341496',       // international, no spaces
    '2348038341496',        // without +
];

$admissionNumber = 'LUC-NGA-002-ADM-8140374';

foreach ($testInputs as $input) {
    $inputContact = preg_replace('/[\s\-\(\)]+/', '', $input);
    if (preg_match('/^\+?234/', $inputContact)) {
        $inputContact = '0' . preg_replace('/^\+?234/', '', $inputContact);
    }
    $inputDigits = preg_replace('/\D/', '', $inputContact);
    
    // Try exact match first
    $student = \App\Models\Student::where('admission_number', $admissionNumber)
        ->where('contact_number', $input)->first();
    
    if (!$student) {
        // Normalized match
        $student = \App\Models\Student::where('admission_number', $admissionNumber)
            ->get()->first(function ($s) use ($inputDigits) {
                $dbDigits = preg_replace('/\D/', '', $s->contact_number);
                if (preg_match('/^234/', $dbDigits) && strlen($dbDigits) > 10) {
                    $dbDigits = '0' . substr($dbDigits, 3);
                }
                return $dbDigits === $inputDigits;
            });
    }
    
    $result = $student ? 'MATCH' : 'NO MATCH';
    echo "Input: '{$input}' → Digits: '{$inputDigits}' → {$result}\n";
}
