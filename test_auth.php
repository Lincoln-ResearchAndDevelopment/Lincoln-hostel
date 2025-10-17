<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing SuperAdmin authentication...\n";

// Test authentication attempt
$credentials = [
    'email' => 'superadmin@linchostel.com',
    'password' => 'SuperAdmin@2025'
];

$superAdmin = App\Models\SuperAdmin::where('email', $credentials['email'])->first();

if (!$superAdmin) {
    echo "❌ SuperAdmin not found in database\n";
    exit(1);
}

echo "✅ SuperAdmin found: {$superAdmin->name}\n";

// Test password verification
if (Hash::check($credentials['password'], $superAdmin->password)) {
    echo "✅ Password verification successful\n";
} else {
    echo "❌ Password verification failed\n";
    exit(1);
}

// Test authentication guard
$guard = Auth::guard('superadmin');
if ($guard->attempt($credentials)) {
    echo "✅ Authentication guard working\n";
    $authenticatedUser = $guard->user();
    echo "✅ Authenticated user: {$authenticatedUser->name}\n";

    // Test logout
    $guard->logout();
    echo "✅ Logout successful\n";
} else {
    echo "❌ Authentication guard failed\n";
    exit(1);
}

echo "✅ All authentication tests passed!\n";
?>
