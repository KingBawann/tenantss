<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

$request = Illuminate\Http\Request::create('/users', 'GET');
$response = $kernel->handle($request);

echo "STATUS: " . $response->status() . "\n";
if ($response->status() !== 200) {
    if (method_exists($response, 'exception') && $response->exception) {
        echo "EXCEPTION: " . $response->exception->getMessage();
    }
}
