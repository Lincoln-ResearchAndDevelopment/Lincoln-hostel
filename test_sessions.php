<?php
$sessionPath = __DIR__ . '/storage/framework/sessions';
echo "Session path: " . $sessionPath . "\n";
echo "Path exists: " . (is_dir($sessionPath) ? 'Yes' : 'No') . "\n";
echo "Path writable: " . (is_writable($sessionPath) ? 'Yes' : 'No') . "\n";

// Try to create a test session file
$testFile = $sessionPath . '/test_session_' . time();
$result = file_put_contents($testFile, 'test');
echo "Test file creation: " . ($result !== false ? 'Success' : 'Failed') . "\n";

// Clean up
if (file_exists($testFile)) {
    unlink($testFile);
    echo "Test file cleaned up\n";
}
?>
