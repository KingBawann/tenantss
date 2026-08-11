<?php
$controllersPath = __DIR__ . '/app/Http/Controllers';

$entities = [
    'Category' => 'categories',
    'Product' => 'products',
    'Customer' => 'customers',
    'Supplier' => 'suppliers',
    'Sale' => 'sales',
    'Purchase' => 'purchases'
];

foreach ($entities as $model => $viewFolder) {
    $variable = strtolower($model);
    $pluralVariable = $viewFolder;
    
    $content = "<?php\n\nnamespace App\Http\Controllers;\n\nuse App\Models\\$model;\nuse Illuminate\Http\Request;\n\nclass {$model}Controller extends Controller\n{\n    public function index()\n    {\n        \$$pluralVariable = $model::all();\n        return view('$viewFolder.index', compact('$pluralVariable'));\n    }\n\n    public function create()\n    {\n        return view('$viewFolder.create');\n    }\n\n    public function store(Request \$request)\n    {\n        $model::create(\$request->all());\n        return redirect()->route('$viewFolder.index');\n    }\n\n    public function show($model \$$variable)\n    {\n        return view('$viewFolder.show', compact('$variable'));\n    }\n\n    public function edit($model \$$variable)\n    {\n        return view('$viewFolder.edit', compact('$variable'));\n    }\n\n    public function update(Request \$request, $model \${$variable})\n    {\n        \${$variable}->update(\$request->all());\n        return redirect()->route('$viewFolder.index');\n    }\n\n    public function destroy($model \${$variable})\n    {\n        \${$variable}->delete();\n        return redirect()->route('$viewFolder.index');\n    }\n}\n";
    
    file_put_contents("$controllersPath/{$model}Controller.php", $content);
}

echo "6 Controllers updated successfully!\n";
