<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Test if SuperAdmin model exists and works
    $superAdmin = App\Models\SuperAdmin::where('email', 'superadmin@linchostel.com')->first();

    if ($superAdmin) {
        echo "SuperAdmin found: {$superAdmin->name}\n";
        echo "Email: {$superAdmin->email}\n";
        echo "Is Master: " . ($superAdmin->is_master ? 'Yes' : 'No') . "\n";
        echo "Permissions: " . implode(', ', $superAdmin->permissions ?? []) . "\n";
    } else {
        echo "No SuperAdmin found with that email.\n";
    }

    echo "SuperAdmin model is working correctly!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
