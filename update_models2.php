<?php

$models = ['Branch', 'Category', 'Product', 'Customer', 'Supplier', 'Sale', 'Purchase', 'InventoryAction', 'LoginLog', 'SaleReturn'];

foreach ($models as $model) {
    $file = 'app/Models/' . $model . '.php';
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, 'use App\Traits\BelongsToTenant;') === false) {
            $content = preg_replace('/namespace App\\\Models;\s*/', "namespace App\Models;\n\nuse App\Traits\BelongsToTenant;\n", $content);
            $content = preg_replace('/class ' . $model . ' extends Model\s*\{/', "class $model extends Model\n{\n    use BelongsToTenant;\n", $content);
            file_put_contents($file, $content);
            echo "Updated $model\n";
        } else {
            echo "Already updated $model\n";
        }
    } else {
        echo "Missing $model\n";
    }
}
