<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$apps = DB::table('hostel_applications')->whereIn('id', [147, 148])->get();
echo "=== TARGET APPLICATIONS ===\n";
foreach ($apps as $a) {
    echo "ID: {$a->id}\n";
    echo "  - Application Number: {$a->application_number}\n";
    echo "  - Full Name: {$a->full_name}\n";
    echo "  - Email: {$a->email}\n";
    echo "  - Student ID: {$a->student_id}\n";
    echo "  - Status: {$a->status}\n";
    echo "  - Reviewed At: {$a->reviewed_at}\n";
    echo "  - Created At: {$a->created_at}\n";
}
