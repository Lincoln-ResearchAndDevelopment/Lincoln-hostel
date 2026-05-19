<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create(
    '/student/login',
    'POST',
    [
        'admission_number' => '54678976',
        'contact_number' => '456788765443',
        '_token' => csrf_token() // Need to handle CSRF or disable it for the test
    ]
);

$response = $kernel->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Redirect URL: " . $response->headers->get('Location') . "\n";

$kernel->terminate($request, $response);
