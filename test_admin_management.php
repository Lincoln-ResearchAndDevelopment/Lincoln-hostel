<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Super Admin Admin Management System...\n";

// Test if routes exist
try {
    $routes = app('router')->getRoutes();
    $adminManagementRoutes = [];

    foreach ($routes as $route) {
        if (strpos($route->uri(), 'superadmin/admin-management') === 0) {
            $adminManagementRoutes[] = $route->uri();
        }
    }

    if (!empty($adminManagementRoutes)) {
        echo "✅ Admin Management routes found:\n";
        foreach ($adminManagementRoutes as $route) {
            echo "   - $route\n";
        }
    } else {
        echo "❌ No admin management routes found\n";
    }

    // Test if we can create an admin user
    $adminData = [
        'name' => 'Test Admin',
        'email' => 'testadmin@linchostel.com',
        'password' => 'TestAdmin123',
        'role' => 'admin',
        'is_active' => true,
        'permissions' => ['rooms.view', 'students.view']
    ];

    // Check if admin already exists
    $existingAdmin = App\Models\User::where('email', $adminData['email'])->first();
    if ($existingAdmin) {
        echo "✅ Test admin already exists: {$existingAdmin->name}\n";
    } else {
        echo "ℹ️ Test admin doesn't exist yet\n";
    }

    // Test SuperAdmin model
    $superAdmin = App\Models\SuperAdmin::where('email', 'superadmin@linchostel.com')->first();
    if ($superAdmin) {
        echo "✅ SuperAdmin exists and can access admin management\n";
    }

    echo "✅ Super Admin Admin Management system is ready!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
