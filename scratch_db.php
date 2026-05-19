<?php
$pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$databases = $pdo->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
print_r($databases);

$dbName = 'hostel_real_import';
if (!in_array($dbName, $databases)) {
    $pdo->exec("CREATE DATABASE $dbName");
    echo "Created database $dbName\n";
}

$sqlFile = 'C:/Users/USER/Desktop/lincoln-hostel/ConnectionToTheRealDb/lincolne_hostel.sql';
if (file_exists($sqlFile)) {
    echo "Found SQL file, importing...\n";
    // We can run mysql CLI if available, otherwise read and exec.
    // Let's just find the mysql binary path if possible
} else {
    echo "SQL file not found.\n";
}
