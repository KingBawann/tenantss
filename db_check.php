<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Tenants: " . \App\Models\Tenant::count() . "\n";
echo "Branches: " . \App\Models\Branch::count() . "\n";
echo "Users: " . \App\Models\User::count() . "\n";
