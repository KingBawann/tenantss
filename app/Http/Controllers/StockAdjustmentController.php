<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_change' => 'required|integer|not_in:0',
            'reason' => 'required|in:damaged,theft,expired,correction,received',
            'note' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($validated['product_id']);

            // Prevent going below 0 unless it's a correction that makes sense (handled by DB/business logic)
            // But we can just enforce it here.
            if ($product->stock_quantity + $validated['quantity_change'] < 0) {
                return back()->with('error', 'Cannot reduce stock below zero.');
            }

            // Create adjustment record
            StockAdjustment::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'quantity_change' => $validated['quantity_change'],
                'reason' => $validated['reason'],
                'note' => $validated['note'],
            ]);

            // Update product stock
            if ($validated['quantity_change'] > 0) {
                $product->increment('stock_quantity', $validated['quantity_change']);
            } else {
                $product->decrement('stock_quantity', abs($validated['quantity_change']));
            }

            DB::commit();
            return back()->with('success', 'Stock adjusted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to adjust stock: ' . $e->getMessage());
        }
    }
}
