<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\InventoryAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('supplier', 'items.product')->latest()->get();
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id'          => 'required|integer|exists:suppliers,id',
            'date'                 => 'required|date',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|integer|exists:products,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.cost'         => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $branchId = auth()->user()->branch_id ?? \App\Models\Branch::first()->id ?? null;
            
            if (!$branchId) {
                $branch = \App\Models\Branch::firstOrCreate(
                    ['name' => 'Main Branch'],
                    ['location' => 'HQ']
                );
                $branchId = $branch->id;
            }

            $total = 0;

            // Pre-calculate total
            foreach ($validated['items'] as $item) {
                $total += $item['cost'] * $item['quantity'];
            }

            // Create the purchase header
            $purchase = Purchase::create([
                'branch_id'   => $branchId,
                'supplier_id' => $validated['supplier_id'],
                'date'        => $validated['date'],
                'total'       => $total,
            ]);

            // Create line items, add stock, log inventory actions
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Create the purchase line item
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id'  => $product->id,
                    'quantity'    => $item['quantity'],
                    'cost'        => $item['cost'],
                ]);

                // Increment stock
                $product->increment('stock_quantity', $item['quantity']);

                // Write inventory audit trail
                InventoryAction::create([
                    'branch_id'  => $branchId,
                    'product_id' => $product->id,
                    'type'       => 'in',
                    'quantity'   => $item['quantity'],
                    'date'       => $validated['date'],
                ]);
            }

            DB::commit();
            session()->flash('success', "Purchase #{$purchase->id} created! Total: \${$total}. Stock added for " . count($validated['items']) . " product(s).");
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create purchase: ' . $e->getMessage());
            return back()->withInput();
        }

        return redirect()->route('purchases.index');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('items.product', 'supplier');
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::all();
        // Usually, we don't allow full editing of items after a purchase is made without reversing stock.
        // For simplicity, we just allow editing the supplier or date.
        return view('purchases.edit', compact('purchase', 'suppliers'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'date'        => 'required|date',
        ]);

        try {
            $purchase->update($validated);
            session()->flash('success', 'Purchase updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update purchase: ' . $e->getMessage());
            return back()->withInput();
        }

        return redirect()->route('purchases.index');
    }

    public function destroy(Purchase $purchase)
    {
        try {
            DB::beginTransaction();

            // Reverse stock for each item before deleting
            foreach ($purchase->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    
                    // Check if decrementing would cause negative stock
                    if ($product->stock_quantity < $item->quantity) {
                        throw new \Exception("Cannot delete purchase. Product \"{$product->name}\" only has {$product->stock_quantity} in stock, but reversing this purchase requires deducting {$item->quantity}.");
                    }

                    $product->decrement('stock_quantity', $item->quantity);

                    // Log the reversal
                    InventoryAction::create([
                        'branch_id'  => $purchase->branch_id,
                        'product_id' => $product->id,
                        'type'       => 'out',
                        'quantity'   => $item->quantity,
                        'date'       => now()->toDateString(),
                    ]);
                }
            }

            $purchase->delete(); // cascade deletes purchase_items
            DB::commit();
            session()->flash('success', 'Purchase deleted and stock reversed!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to delete purchase: ' . $e->getMessage());
        }
        return redirect()->route('purchases.index');
    }
}
