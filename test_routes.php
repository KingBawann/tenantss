<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Web Tenant Index: " . route('tenants.index') . "\n";
echo "API Tenant Index: " . route('api.tenants.index') . "\n";
