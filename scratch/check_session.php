<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SESSION CONFIGURATION ===\n";
echo "Session driver: " . config('session.driver') . "\n";
echo "Session cookie name: " . config('session.cookie') . "\n";
echo "Session domain: " . (config('session.domain') ?: 'null (any)') . "\n";
echo "Session secure: " . (config('session.secure') ? 'true' : 'false') . "\n";
echo "Session same_site: " . config('session.same_site') . "\n";
echo "Session lifetime: " . config('session.lifetime') . " minutes\n";
echo "Session path: " . config('session.path') . "\n";
echo "Session http_only: " . (config('session.http_only') ? 'true' : 'false') . "\n";

echo "\n=== SESSIONS TABLE CHECK ===\n";
$hasTable = Illuminate\Support\Facades\Schema::hasTable('sessions');
echo "Sessions table exists: " . ($hasTable ? 'YES' : 'NO') . "\n";

if ($hasTable) {
    $count = Illuminate\Support\Facades\DB::table('sessions')->count();
    echo "Active sessions: {$count}\n";
    
    $columns = Illuminate\Support\Facades\Schema::getColumnListing('sessions');
    echo "Columns: " . implode(', ', $columns) . "\n";
    
    // Show recent sessions
    $recent = Illuminate\Support\Facades\DB::table('sessions')
        ->orderBy('last_activity', 'desc')
        ->limit(3)
        ->get(['id', 'user_id', 'ip_address', 'last_activity']);
    echo "\nRecent sessions:\n";
    foreach ($recent as $s) {
        $time = date('Y-m-d H:i:s', $s->last_activity);
        echo "  ID: " . substr($s->id, 0, 20) . "... | User: " . ($s->user_id ?? 'guest') . " | IP: {$s->ip_address} | Last: {$time}\n";
    }
}

echo "\n=== APP CONFIG ===\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "APP_KEY: " . (config('app.key') ? 'SET (' . strlen(config('app.key')) . ' chars)' : 'NOT SET!') . "\n";
echo "APP_ENV: " . config('app.env') . "\n";

echo "\n=== CSRF MIDDLEWARE CHECK ===\n";
$kernelClass = app(\App\Http\Kernel::class);
echo "Kernel class loaded: YES\n";
