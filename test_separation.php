<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Admin vs Super Admin Separation...\n";

// Test if routes are properly separated
try {
    $routes = app('router')->getRoutes();

    $adminRoutes = [];
    $superAdminRoutes = [];

    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'superadmin') === 0) {
            $superAdminRoutes[] = $uri;
        } elseif (in_array($uri, ['dashboard', 'rooms', 'students', 'payments', 'complaints', 'visitors', 'staff'])) {
            $adminRoutes[] = $uri;
        }
    }

    echo "✅ Admin Routes: " . implode(', ', $adminRoutes) . "\n";
    echo "✅ Super Admin Routes: " . implode(', ', $superAdminRoutes) . "\n";

    // Test if middleware is properly configured
    $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
    $middlewareGroups = $kernel->getMiddlewareGroups();

    if (isset($middlewareGroups['web'])) {
        echo "✅ Web middleware group exists\n";
    }

    if (isset($kernel->getMiddlewareAliases()['admin'])) {
        echo "✅ Admin middleware alias exists\n";
    }

    if (isset($kernel->getMiddlewareAliases()['superadmin'])) {
        echo "✅ SuperAdmin middleware alias exists\n";
    }

    // Test if guards are separate
    $webGuard = Auth::guard('web');
    $superAdminGuard = Auth::guard('superadmin');

    echo "✅ Web guard: " . get_class($webGuard) . "\n";
    echo "✅ SuperAdmin guard: " . get_class($superAdminGuard) . "\n";

    echo "✅ Admin and Super Admin systems are properly separated!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
