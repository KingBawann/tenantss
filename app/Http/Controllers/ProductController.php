<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
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

        // Sales velocity (units sold in last 7 and 30 days)
        $soldThisWeek = \App\Models\SaleItem::where('product_id', $product->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->sum('quantity');

        $soldThisMonth = \App\Models\SaleItem::where('product_id', $product->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('quantity');

        return view('products.show', compact('product', 'inventoryActions', 'soldThisWeek', 'soldThisMonth'));
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
}
