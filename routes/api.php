<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Health Check Endpoint
|--------------------------------------------------------------------------
*/
Route::get('/health', function () {
    $dbStatus = 'ok';
    try {
        DB::connection()->getPdo();
    } catch (\Exception $e) {
        $dbStatus = 'error: ' . $e->getMessage();
    }

    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'environment' => config('app.env'),
        'debug' => config('app.debug'),
        'database' => $dbStatus,
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('health');
