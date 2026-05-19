<?php
$files = glob(__DIR__ . '/../app/Mail/*.php');
foreach($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'implements ShouldQueue') === false && preg_match('/class\s+([a-zA-Z0-9_]+)\s+extends\s+Mailable/ism', $content)) {
        if (strpos($content, 'use Illuminate\Contracts\Queue\ShouldQueue;') === false) {
            $content = preg_replace('/(use Illuminate\\\\Mail\\\\Mailable;)/', "use Illuminate\\Contracts\\Queue\\ShouldQueue;\n$1", $content);
        }
        $content = preg_replace('/class\s+([a-zA-Z0-9_]+)\s+extends\s+Mailable/ism', 'class $1 extends Mailable implements ShouldQueue', $content);
        file_put_contents($file, $content);
        echo "Updated " . basename($file) . "\n";
    }
}
echo "Done.\n";
