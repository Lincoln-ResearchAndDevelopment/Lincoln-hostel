<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ACTIVE DATABASE CONFIG ===\n";
print_r(config('database.connections.mysql'));

echo "\n=== ACTIVE MAIL CONFIG ===\n";
print_r(config('mail'));
