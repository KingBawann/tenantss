<?php

$viewsPath = __DIR__ . '/resources/views';

$entities = [
    'categories' => ['name'],
    'products' => ['name', 'price', 'cost_price', 'barcode'],
    'customers' => ['name', 'phone', 'balance'],
    'suppliers' => ['name', 'phone'],
    'sales' => ['total', 'payment_status'],
    'purchases' => ['total', 'date'],
];

foreach ($entities as $entity => $fields) {
    $entityDir = $viewsPath . '/' . $entity;
    if (!is_dir($entityDir)) {
        mkdir($entityDir, 0777, true);
    }
    
    $singleEntity = rtrim($entity, 's');
    if ($entity == 'categories') $singleEntity = 'category';
    
    // index.blade.php
    $indexContent = "<x-app-layout>\n    <x-slot name=\"header\">\n        <h2 class=\"font-semibold text-xl text-gray-800 leading-tight\">" . ucfirst($entity) . "</h2>\n    </x-slot>\n\n    <div class=\"py-12\">\n        <div class=\"max-w-7xl mx-auto sm:px-6 lg:px-8\">\n            <div class=\"bg-white overflow-hidden shadow-sm sm:rounded-lg p-6\">\n                <a href=\"{{ route('$entity.create') }}\" class=\"text-blue-500 underline mb-4 inline-block\">Create New " . ucfirst($singleEntity) . "</a>\n                <table class=\"w-full text-left border-collapse\">\n                    <thead>\n                        <tr>\n                            <th class=\"border-b p-2\">ID</th>\n";
    foreach ($fields as $field) {
        $indexContent .= "                            <th class=\"border-b p-2\">" . ucfirst(str_replace('_', ' ', $field)) . "</th>\n";
    }
    $indexContent .= "                            <th class=\"border-b p-2\">Actions</th>\n                        </tr>\n                    </thead>\n                    <tbody>\n                        @foreach($$entity as \$item)\n                        <tr>\n                            <td class=\"border-b p-2\">{{ \$item->id }}</td>\n";
    foreach ($fields as $field) {
        $indexContent .= "                            <td class=\"border-b p-2\">{{ \$item->$field }}</td>\n";
    }
    $indexContent .= "                            <td class=\"border-b p-2\">\n                                <a href=\"{{ route('$entity.show', \$item->id) }}\" class=\"text-blue-500\">View</a> |\n                                <a href=\"{{ route('$entity.edit', \$item->id) }}\" class=\"text-green-500\">Edit</a>\n                            </td>\n                        </tr>\n                        @endforeach\n                    </tbody>\n                </table>\n            </div>\n        </div>\n    </div>\n</x-app-layout>";
    file_put_contents("$entityDir/index.blade.php", $indexContent);
    
    // create.blade.php
    $createContent = "<x-app-layout>\n    <x-slot name=\"header\">\n        <h2 class=\"font-semibold text-xl text-gray-800 leading-tight\">Create " . ucfirst($singleEntity) . "</h2>\n    </x-slot>\n\n    <div class=\"py-12\">\n        <div class=\"max-w-7xl mx-auto sm:px-6 lg:px-8\">\n            <div class=\"bg-white overflow-hidden shadow-sm sm:rounded-lg p-6\">\n                <form action=\"{{ route('$entity.store') }}\" method=\"POST\">\n                    @csrf\n";
    foreach ($fields as $field) {
        $createContent .= "                    <div class=\"mb-4\">\n                        <label class=\"block font-bold mb-1\">" . ucfirst(str_replace('_', ' ', $field)) . "</label>\n                        <input type=\"text\" name=\"$field\" class=\"border rounded p-2 w-full\" required>\n                    </div>\n";
    }
    $createContent .= "                    <button type=\"submit\" class=\"bg-blue-500 text-white px-4 py-2 rounded\">Save</button>\n                    <a href=\"{{ route('$entity.index') }}\" class=\"ml-4 text-gray-500\">Cancel</a>\n                </form>\n            </div>\n        </div>\n    </div>\n</x-app-layout>";
    file_put_contents("$entityDir/create.blade.php", $createContent);

    // edit.blade.php
    $editContent = "<x-app-layout>\n    <x-slot name=\"header\">\n        <h2 class=\"font-semibold text-xl text-gray-800 leading-tight\">Edit " . ucfirst($singleEntity) . "</h2>\n    </x-slot>\n\n    <div class=\"py-12\">\n        <div class=\"max-w-7xl mx-auto sm:px-6 lg:px-8\">\n            <div class=\"bg-white overflow-hidden shadow-sm sm:rounded-lg p-6\">\n                <form action=\"{{ route('$entity.update', \${$singleEntity}->id) }}\" method=\"POST\">\n                    @csrf\n                    @method('PUT')\n";
    foreach ($fields as $field) {
        $editContent .= "                    <div class=\"mb-4\">\n                        <label class=\"block font-bold mb-1\">" . ucfirst(str_replace('_', ' ', $field)) . "</label>\n                        <input type=\"text\" name=\"$field\" value=\"{{ \${$singleEntity}->$field }}\" class=\"border rounded p-2 w-full\" required>\n                    </div>\n";
    }
    $editContent .= "                    <button type=\"submit\" class=\"bg-blue-500 text-white px-4 py-2 rounded\">Update</button>\n                    <a href=\"{{ route('$entity.index') }}\" class=\"ml-4 text-gray-500\">Cancel</a>\n                </form>\n            </div>\n        </div>\n    </div>\n</x-app-layout>";
    file_put_contents("$entityDir/edit.blade.php", $editContent);

    // show.blade.php
    $showContent = "<x-app-layout>\n    <x-slot name=\"header\">\n        <h2 class=\"font-semibold text-xl text-gray-800 leading-tight\">View " . ucfirst($singleEntity) . "</h2>\n    </x-slot>\n\n    <div class=\"py-12\">\n        <div class=\"max-w-7xl mx-auto sm:px-6 lg:px-8\">\n            <div class=\"bg-white overflow-hidden shadow-sm sm:rounded-lg p-6\">\n";
    foreach ($fields as $field) {
        $showContent .= "                <div class=\"mb-4\">\n                    <strong class=\"block\">" . ucfirst(str_replace('_', ' ', $field)) . ":</strong>\n                    <span>{{ \${$singleEntity}->$field }}</span>\n                </div>\n";
    }
    $showContent .= "                <a href=\"{{ route('$entity.index') }}\" class=\"text-blue-500 underline\">Back to List</a>\n            </div>\n        </div>\n    </div>\n</x-app-layout>";
    file_put_contents("$entityDir/show.blade.php", $showContent);
}

echo "24 Views created successfully!\n";
