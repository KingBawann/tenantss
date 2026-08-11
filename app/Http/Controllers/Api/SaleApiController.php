<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\InventoryAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleApiController extends Controller
{
    public function index(Request $request)
    {
        $sales = Sale::with(['customer', 'items.product'])
            ->when($request->query('payment_status'), fn($q, $s) => $q->where('payment_status', $s))
            ->latest()
            ->paginate($request->query('per_page', 15));

        return SaleResource::collection($sales);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|integer|exists:branches,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'customer_id' => 'nullable|integer|exists:customers,id',
            'payment_status' => 'required|string|in:paid,pending,cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {
            $total = 0;

            foreach ($validated['items'] as $item) {
                $total += $item['quantity'] * $item['price'];
            }

            $branchId = $validated['branch_id'] ?? $request->user()->branch_id ?? \App\Models\Branch::first()->id ?? null;
            
            if (!$branchId) {
                $branch = \App\Models\Branch::firstOrCreate(
                    ['name' => 'Main Branch'],
                    ['location' => 'HQ']
                );
                $branchId = $branch->id;
            }

            $sale = Sale::create([
                'branch_id' => $branchId,
                'user_id' => $validated['user_id'] ?? $request->user()->id,
                'customer_id' => $validated['customer_id'] ?? null,
                'total' => $total,
                'payment_status' => $validated['payment_status'],
            ]);

            foreach ($validated['items'] as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $product = Product::where('id', $item['product_id'])->lockForUpdate()->firstOrFail();
                
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for \"{$product->name}\". Available: {$product->stock_quantity}, Requested: {$item['quantity']}");
                }

                $product->decrement('stock_quantity', $item['quantity']);

                InventoryAction::create([
                    'product_id' => $item['product_id'],
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'date' => now()->toDateString(),
                    'note' => "API Sale #{$sale->id}",
                ]);
            }

            return new SaleResource($sale->load(['customer', 'items.product']));
        });
    }

    public function show(Sale $sale)
    {
        return new SaleResource($sale->load(['customer', 'items.product']));
    }

    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                $item->product->increment('stock_quantity', $item->quantity);

                InventoryAction::create([
                    'product_id' => $item->product_id,
                    'type' => 'in',
                    'quantity' => $item->quantity,
                    'date' => now()->toDateString(),
                    'note' => "API Sale #{$sale->id} reversed",
                ]);
            }
            $sale->items()->delete();
            $sale->delete();
        });

        return response()->json(['message' => 'Sale reversed and deleted.'], 200);
    }
}
