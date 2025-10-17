<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $adminCount = App\Models\User::where('role', 'admin')->orWhere('is_admin', 1)->count();
    echo "Current Admin Count: $adminCount\n";

    $activeAdmins = App\Models\User::where('role', 'admin')->orWhere('is_admin', 1)->where('is_active', 1)->count();
    echo "Active Admins: $activeAdmins\n";

    $inactiveAdmins = App\Models\User::where('role', 'admin')->orWhere('is_admin', 1)->where('is_active', 0)->count();
    echo "Inactive Admins: $inactiveAdmins\n";

    $admins = App\Models\User::where('role', 'admin')->orWhere('is_admin', 1)->get();
    if ($admins->count() > 0) {
        echo "\nAdmin Users:\n";
        foreach ($admins as $admin) {
            echo "- {$admin->name} ({$admin->email}) - " . ($admin->is_active ? 'Active' : 'Inactive') . "\n";
        }
    } else {
        echo "\nNo admin users found in database.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
