<?php
$models = ['Branch', 'Category', 'Customer', 'Product', 'Purchase', 'Sale', 'Supplier', 'User'];
foreach ($models as $m) {
    $path = __DIR__ . '/app/Models/' . $m . '.php';
    $content = file_get_contents($path);
    
    // 1. Add import
    if (!str_contains($content, 'use App\Models\Traits\BelongsToTenant;')) {
        $content = str_replace("namespace App\Models;\n", "namespace App\Models;\n\nuse App\Models\\Traits\\BelongsToTenant;", $content);
    }
    
    // 2. Add trait inside class
    if (!str_contains($content, 'use BelongsToTenant;')) {
        // Find the first line starting with '    use ' inside the class
        $content = preg_replace('/(\n\s*use\s+HasFactory.*?;)/s', "$1\n    use BelongsToTenant;", $content, 1);
    }
    
    // 3. Remove old tenant() relation
    $content = preg_replace('/public function tenant\(\)\s*\{\s*return \$this->belongsTo\(Tenant::class\);\s*\}/m', '', $content);
    
    file_put_contents($path, $content);
    echo "Updated $m\n";
}
