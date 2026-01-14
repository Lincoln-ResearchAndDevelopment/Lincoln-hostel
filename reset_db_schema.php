<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Forcing database reset...\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
    $tables = DB::select('SHOW TABLES');
    $dbName = env('DB_DATABASE', 'hostel');
    $colname = "Tables_in_" . $dbName;

    foreach($tables as $table) {
        // Handle different fetch modes if necessary, but object access is standard in Laravel
        $tableName = $table->$colname ?? reset($table); // Fallback to first column
        
        if ($tableName) {
            echo "Dropping table: $tableName\n";
            Schema::dropIfExists($tableName);
        }
    }

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "All tables dropped.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
