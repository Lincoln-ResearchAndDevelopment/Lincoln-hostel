<?php
$host = '127.0.0.1';
$db   = 'lincoln_real';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create DB
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database created.\n";
    
    // Connect to the new DB
    $pdo->exec("USE `$db`");
    
    // Read SQL file
    $sqlFile = 'C:/Users/USER/Desktop/lincoln-hostel/ConnectionToTheRealDb/lincolne_hostel.sql';
    if (!file_exists($sqlFile)) {
        die("SQL file not found.\n");
    }
    
    echo "Importing SQL...\n";
    $sql = file_get_contents($sqlFile);
    
    $pdo->exec($sql);
    echo "Import successful.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
