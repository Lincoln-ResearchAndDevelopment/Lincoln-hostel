<?php
$sqlFile = 'C:/Users/USER/Desktop/lincoln-hostel/ConnectionToTheRealDb/lincolne_hostel.sql';
$sql = file_get_contents($sqlFile);
preg_match_all('/CREATE TABLE `(.*?)` \((.*?)\) ENGINE=/s', $sql, $matches);

foreach ($matches[1] as $index => $tableName) {
    echo "TABLE: $tableName\n";
    $columns = explode("\n", trim($matches[2][$index]));
    foreach ($columns as $column) {
        if (!empty(trim($column))) {
            echo "  " . trim($column) . "\n";
        }
    }
    echo "--------------------------\n";
}
