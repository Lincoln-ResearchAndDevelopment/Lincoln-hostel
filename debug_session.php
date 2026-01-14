<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Session Driver: " . config('session.driver') . "\n";
echo "Session Domain: " . var_export(config('session.domain'), true) . "\n";
echo "Session Secure: " . var_export(config('session.secure'), true) . "\n";
echo "Session Lifetime: " . config('session.lifetime') . "\n";
echo "SameSite Attribute: " . config('session.same_site') . "\n";
echo "HttpOnly: " . var_export(config('session.http_only'), true) . "\n";

$sessionPath = storage_path('framework/sessions');
echo "Session Path: " . $sessionPath . "\n";
echo "Session Path Writable: " . (is_writable($sessionPath) ? 'Yes' : 'No') . "\n";

// Test writing to session path
$testFile = $sessionPath . '/test_write_permission';
if (@file_put_contents($testFile, 'test')) {
    echo "Test write successful.\n";
    unlink($testFile);
} else {
    echo "Test write FAILED.\n";
}
