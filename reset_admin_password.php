<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $email = 'admin@linchostel.com';
    $password = 'admin12345';

    $user = User::where('email', $email)->first();

    if ($user) {
        $user->password = Hash::make($password);
        $user->role = 'admin';
        $user->is_admin = true;
        $user->save();
        echo "Admin user updated. Password reset to: $password\n";
    } else {
        User::create([
            'name' => 'LincHostel Admin',
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'is_admin' => true,
        ]);
        echo "Admin user created. Password set to: $password\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
