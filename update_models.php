<?php

$models = ['Branch', 'Category', 'Customer', 'Product', 'Purchase', 'Sale', 'Supplier', 'LoginLog', 'User', 'InventoryAction'];
foreach ($models as $model) {
    $path = 'D:/xampp/htdocs/Laravel/tenant/app/Models/' . $model . '.php';
    if (!file_exists($path)) {
        echo "Missing $path\n";
        continue;
    }
    $content = file_get_contents($path);
    
    // add tenant_id to fillable array if not exists
    if (strpos($content, '#[Fillable') !== false) {
        if (strpos($content, "'tenant_id'") === false) {
            $content = preg_replace('/#\[Fillable\(\[/', '#[Fillable([\'tenant_id\', ', $content);
        }
    } else {
        // Find protected $fillable = [ ... ];
        if (strpos($content, "'tenant_id'") === false && strpos($content, '$fillable') !== false) {
            $content = preg_replace('/protected\s+\$fillable\s*=\s*\[/', 'protected $fillable = [\'tenant_id\', ', $content);
        }
    }
    
    // add tenant relationship if not exists
    if (strpos($content, 'function tenant()') === false) {
        $content = preg_replace('/}(?!.*})/s', "    public function tenant() { return \$this->belongsTo(\App\Models\Tenant::class); }\n}\n", $content);
    }
    
    file_put_contents($path, $content);
    echo "Updated $model\n";
}
