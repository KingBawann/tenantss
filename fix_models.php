<?php
$models = ['Branch', 'Category', 'Customer', 'Product', 'Purchase', 'Sale', 'Supplier'];
foreach ($models as $m) {
    $path = __DIR__ . '/app/Models/' . $m . '.php';
    if (!file_exists($path)) continue;
    
    $content = file_get_contents($path);
    
    if (!str_contains($content, 'use App\Models\Traits\BelongsToTenant;')) {
        $content = str_replace("namespace App\Models;\n", "namespace App\Models;\n\nuse App\Models\\Traits\\BelongsToTenant;\n", $content);
    }
    
    // Add use BelongsToTenant; inside the class body if not present
    if (!str_contains($content, 'use BelongsToTenant;')) {
        // Find the class opening brace
        $content = preg_replace('/(class\s+[a-zA-Z0-9_]+\s+extends\s+[a-zA-Z0-9_]+\s*\{)/', "$1\n    use BelongsToTenant;\n", $content);
    }
    
    file_put_contents($path, $content);
    echo "Fixed $m\n";
}
