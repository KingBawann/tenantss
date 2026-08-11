<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PurchaseResource;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\InventoryAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseApiController extends Controller
{
    public function index(Request $request)
    {
        $purchases = Purchase::with(['supplier', 'items.product'])
            ->when($request->query('supplier_id'), fn($q, $id) => $q->where('supplier_id', $id))
            ->latest()
            ->paginate($request->query('per_page', 15));

        return PurchaseResource::collection($purchases);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|integer|exists:branches,id',
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {
            $total = 0;

            foreach ($validated['items'] as $item) {
                $total += $item['quantity'] * $item['cost'];
            }

            $branchId = $validated['branch_id'] ?? $request->user()->branch_id ?? \App\Models\Branch::first()->id ?? null;
            
            if (!$branchId) {
                $branch = \App\Models\Branch::firstOrCreate(
                    ['name' => 'Main Branch'],
                    ['location' => 'HQ']
                );
                $branchId = $branch->id;
            }

            $purchase = Purchase::create([
                'branch_id' => $branchId,
                'supplier_id' => $validated['supplier_id'],
                'date' => $validated['date'],
                'total' => $total,
            ]);

            foreach ($validated['items'] as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost' => $item['cost'],
                ]);

                $product = Product::findOrFail($item['product_id']);
                $product->increment('stock_quantity', $item['quantity']);

                InventoryAction::create([
                    'product_id' => $item['product_id'],
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'date' => $validated['date'],
                    'note' => "API Purchase #{$purchase->id}",
                ]);
            }

            return new PurchaseResource($purchase->load(['supplier', 'items.product']));
        });
    }

    public function show(Purchase $purchase)
    {
        return new PurchaseResource($purchase->load(['supplier', 'items.product']));
    }

    public function destroy(Purchase $purchase)
    {
        DB::transaction(function () use ($purchase) {
            foreach ($purchase->items as $item) {
                $item->product->decrement('stock_quantity', $item->quantity);

                InventoryAction::create([
                    'product_id' => $item->product_id,
                    'type' => 'out',
                    'quantity' => $item->quantity,
                    'date' => now()->toDateString(),
                    'note' => "API Purchase #{$purchase->id} reversed",
                ]);
            }
            $purchase->items()->delete();
            $purchase->delete();
        });

        return response()->json(['message' => 'Purchase reversed and deleted.'], 200);
    }
}
