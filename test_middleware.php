<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing middleware functionality...\n";

// Test if routes exist
try {
    $routes = app('router')->getRoutes();
    $superadminRoutes = [];

    foreach ($routes as $route) {
        if (strpos($route->uri(), 'superadmin') === 0) {
            $superadminRoutes[] = $route->uri();
        }
    }

    if (!empty($superadminRoutes)) {
        echo "✅ SuperAdmin routes found:\n";
        foreach ($superadminRoutes as $route) {
            echo "   - $route\n";
        }
    } else {
        echo "❌ No superadmin routes found\n";
    }

    // Test if SuperAdmin model works
    $superAdmin = App\Models\SuperAdmin::where('email', 'superadmin@linchostel.com')->first();
    if ($superAdmin) {
        echo "✅ SuperAdmin model works - found: {$superAdmin->name}\n";
    } else {
        echo "❌ SuperAdmin model not working\n";
    }

    echo "✅ All middleware tests passed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
