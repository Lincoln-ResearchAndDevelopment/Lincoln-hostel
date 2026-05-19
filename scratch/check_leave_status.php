<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\LeaveRequest;

$latest = LeaveRequest::latest()->first();
if ($latest) {
    echo "SUCCESS: Latest Leave Request ID {$latest->id} is status '{$latest->status}'\n";
} else {
    echo "ERROR: No leave requests found.\n";
}
