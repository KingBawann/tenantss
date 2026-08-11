<?php

namespace App\Http\Controllers;

use App\Models\SaleReturn;
use Illuminate\Http\Request;

class SaleReturnController extends Controller
{
    public function index()
    {
        $returns = SaleReturn::with(['sale', 'user'])->latest()->paginate(10);
        return view('sale-returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $saleId = $request->get('sale_id');
        if (!$saleId) {
            return redirect()->route('sales.index')->with('error', 'Please select a sale to return.');
        }

        $sale = \App\Models\Sale::with('items.product')->findOrFail($saleId);
        
        return view('sale-returns.create', compact('sale'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'reason'  => 'nullable|string|max:255',
            'items'   => 'required|array',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.return_quantity' => 'required|integer|min:0',
        ]);

        $sale = \App\Models\Sale::with('items')->findOrFail($validated['sale_id']);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $totalReturnAmount = 0;
            $itemsToReturn = [];

            foreach ($validated['items'] as $itemData) {
                if ($itemData['return_quantity'] <= 0) continue;

                $saleItem = $sale->items->firstWhere('id', $itemData['sale_item_id']);
                
                // Calculate previously returned quantity for this item to prevent over-returning
                $previouslyReturned = \App\Models\SaleReturnItem::where('sale_item_id', $saleItem->id)->sum('quantity');
                $maxReturnable = $saleItem->quantity - $previouslyReturned;

                if ($itemData['return_quantity'] > $maxReturnable) {
                    throw new \Exception("Cannot return more than purchased for {$saleItem->product->name}. Max returnable: {$maxReturnable}");
                }

                $itemsToReturn[] = [
                    'sale_item' => $saleItem,
                    'quantity' => $itemData['return_quantity'],
                    'price' => $saleItem->price,
                ];

                $totalReturnAmount += ($saleItem->price * $itemData['return_quantity']);
            }

            if (count($itemsToReturn) === 0) {
                throw new \Exception("No items selected for return.");
            }

            $userId = auth()->id() ?? \App\Models\User::first()->id ?? 1;
            $tenantId = auth()->user()->tenant_id ?? 1;

            $saleReturn = SaleReturn::create([
                'tenant_id' => $tenantId,
                'sale_id' => $sale->id,
                'user_id' => $userId,
                'reason' => $validated['reason'] ?? 'Customer return',
                'total' => $totalReturnAmount,
            ]);

            foreach ($itemsToReturn as $returnData) {
                $saleItem = $returnData['sale_item'];
                $product = \App\Models\Product::where('id', $saleItem->product_id)->lockForUpdate()->firstOrFail();

                // Create return item
                \App\Models\SaleReturnItem::create([
                    'sale_return_id' => $saleReturn->id,
                    'sale_item_id' => $saleItem->id,
                    'product_id' => $product->id,
                    'quantity' => $returnData['quantity'],
                    'price' => $returnData['price'],
                ]);

                // Restore stock
                $product->increment('stock_quantity', $returnData['quantity']);

                // Log inventory action
                \App\Models\InventoryAction::create([
                    'tenant_id' => $tenantId,
                    'product_id' => $product->id,
                    'user_id' => $userId,
                    'action_type' => 'sale_return',
                    'quantity' => $returnData['quantity'],
                    'reference_type' => 'sale_return',
                    'reference_id' => $saleReturn->id,
                    'notes' => 'Returned from sale #' . $sale->id
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('sale-returns.show', $saleReturn)->with('success', 'Return processed successfully.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(SaleReturn $saleReturn)
    {
        $saleReturn->load(['sale', 'user', 'items.product']);
        return view('sale-returns.show', compact('saleReturn'));
    }
}
