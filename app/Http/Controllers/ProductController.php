<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stock_status')) {
            match ($request->stock_status) {
                'out'  => $query->where('stock_quantity', 0),
                'low'  => $query->whereBetween('stock_quantity', [1, 5]),
                'ok'   => $query->where('stock_quantity', '>', 5),
                default => null,
            };
        }

        $products   = $query->paginate(30)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|integer',
            'new_category_name' => 'nullable|string|max:255',
            'barcode' => 'nullable|string',
            'price' => 'required|numeric',
            'cost_price' => 'required|numeric',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        try {
            // Handle inline category creation
            if (!empty($validated['new_category_name'])) {
                // Ensure tenant_id is used for multi-tenancy if applicable, or rely on model boot.
                // Assuming Category belongs to a tenant like Product.
                $category = \App\Models\Category::firstOrCreate(
                    ['name' => $validated['new_category_name'], 'tenant_id' => auth()->user()->tenant_id ?? 1]
                );
                $validated['category_id'] = $category->id;
            }
            unset($validated['new_category_name']);

            Product::create($validated);
            session()->flash('success', 'Product created successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create product: ' . $e->getMessage());
            return back()->withInput();
        }

        return redirect()->route('products.index');
    }

    public function show(Product $product)
    {
        $product->load('category');

        $inventoryActions = \App\Models\InventoryAction::with('user')
            ->where('product_id', $product->id)
            ->latest()
            ->paginate(15);

        $stockAdjustments = \App\Models\StockAdjustment::with('user')
            ->where('product_id', $product->id)
            ->latest()
            ->paginate(15, ['*'], 'adjustments_page');

        // Sales velocity (units sold in last 7 and 30 days)
        $soldThisWeek = \App\Models\SaleItem::where('product_id', $product->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->sum('quantity');

        $soldThisMonth = \App\Models\SaleItem::where('product_id', $product->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('quantity');

        return view('products.show', compact('product', 'inventoryActions', 'stockAdjustments', 'soldThisWeek', 'soldThisMonth'));
    }

    public function edit(Product $product)
    {
        $categories = \App\Models\Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|integer',
            'new_category_name' => 'nullable|string|max:255',
            'barcode' => 'nullable|string',
            'price' => 'required|numeric',
            'cost_price' => 'required|numeric',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        try {
            // Handle inline category creation
            if (!empty($validated['new_category_name'])) {
                $category = \App\Models\Category::firstOrCreate(
                    ['name' => $validated['new_category_name'], 'tenant_id' => auth()->user()->tenant_id ?? 1]
                );
                $validated['category_id'] = $category->id;
            }
            unset($validated['new_category_name']);

            $product->update($validated);
            session()->flash('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update product: ' . $e->getMessage());
            return back()->withInput();
        }

        return redirect()->route('products.index');
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
            session()->flash('success', 'Product deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete product: ' . $e->getMessage());
        }
        return redirect()->route('products.index');
    }

    // ─── CSV Bulk Import ──────────────────────────────────────────────────────

    public function showImport()
    {
        return view('products.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file   = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Read header row
        $header = fgetcsv($handle);
        if (!$header) {
            return back()->with('error', 'The CSV file is empty or invalid.');
        }

        // Normalize header keys: lowercase, trim spaces
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        $required = ['name', 'price', 'cost_price', 'stock_quantity'];
        $missing  = array_diff($required, $header);

        if (!empty($missing)) {
            fclose($handle);
            return back()->with('error', 'Missing required columns: ' . implode(', ', $missing) . '. Download the template for the correct format.');
        }

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, array_pad($row, count($header), null));

                // Skip blank rows
                if (empty(trim($data['name'] ?? ''))) {
                    $skipped++;
                    continue;
                }

                // Resolve category
                $categoryId = null;
                if (!empty($data['category'])) {
                    $category   = Category::firstOrCreate(
                        ['name' => trim($data['category']), 'tenant_id' => auth()->user()->tenant_id ?? 1]
                    );
                    $categoryId = $category->id;
                }

                Product::updateOrCreate(
                    ['barcode' => !empty($data['barcode']) ? trim($data['barcode']) : null,
                     'name'    => trim($data['name'])],
                    [
                        'category_id'    => $categoryId,
                        'barcode'        => !empty($data['barcode']) ? trim($data['barcode']) : null,
                        'price'          => (float) ($data['price'] ?? 0),
                        'cost_price'     => (float) ($data['cost_price'] ?? 0),
                        'stock_quantity' => (int) ($data['stock_quantity'] ?? 0),
                        'reorder_point'  => isset($data['reorder_point']) ? (int) $data['reorder_point'] : 5,
                        'description'    => $data['description'] ?? null,
                    ]
                );
                $imported++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        fclose($handle);

        return redirect()->route('products.index')
            ->with('success', "Import complete! {$imported} products imported/updated. {$skipped} rows skipped.");
    }
}
