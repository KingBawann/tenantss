<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\InventoryAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with('customer', 'items.product')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', $search)
                  ->orWhereHas('customer', function($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $sales = $query->paginate(20)->withQueryString();
        
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        if (auth()->user()->tenant->is_master ?? false) {
            return redirect()->route('dashboard')->with('error', 'Master tenants cannot access the POS system.');
        }

        $products = Product::with('category')->get()->append('expiry_status');
        $categories = \App\Models\Category::all();
        $customers = Customer::all();
        return view('sales.create', compact('products', 'categories', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_status'       => 'required|string|in:paid,partial,pending',
            'payment_method'       => 'required|string|in:cash,card,split',
            'customer_id'          => 'nullable|integer|exists:customers,id',
            'discount_type'        => 'required|string|in:none,percentage,fixed',
            'discount_value'       => 'nullable|numeric|min:0',
            'amount_tendered'      => 'nullable|numeric|min:0',
            'cash_amount'          => 'nullable|numeric|min:0',
            'card_amount'          => 'nullable|numeric|min:0',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|integer|exists:products,id',
            'items.*.quantity'     => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $userId   = auth()->id() ?? \App\Models\User::first()->id ?? 1;
            $branchId = auth()->user()->branch_id ?? \App\Models\Branch::first()->id ?? null;
            
            if (!$branchId) {
                $branch = \App\Models\Branch::firstOrCreate(
                    ['name' => 'Main Branch'],
                    ['location' => 'HQ']
                );
                $branchId = $branch->id;
            }

            $total = 0;

            // Pre-check stock availability with Pessimistic Locking
            foreach ($validated['items'] as $item) {
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->firstOrFail();
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for \"{$product->name}\". Available: {$product->stock_quantity}, Requested: {$item['quantity']}");
                }
                $total += $product->price * $item['quantity'];
            }

            // Create the sale header
            $sale = Sale::create([
                'branch_id'      => $branchId,
                'user_id'        => $userId,
                'customer_id'    => $validated['customer_id'] ?? null,
                'total'          => $total,
                'discount_type'  => $validated['discount_type'],
                'discount_value' => $validated['discount_value'] ?? 0,
                'payment_status' => $validated['payment_status'],
                'payment_method' => $validated['payment_method'],
                'amount_tendered'=> $validated['amount_tendered'] ?? null,
                'cash_amount'    => $validated['cash_amount'] ?? null,
                'card_amount'    => $validated['card_amount'] ?? null,
            ]);

            // Create line items, deduct stock, log inventory actions
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Create the sale line item (snapshot the price at time of sale)
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price,
                ]);

                // Deduct stock
                $product->decrement('stock_quantity', $item['quantity']);

                // Write inventory audit trail
                InventoryAction::create([
                    'branch_id'  => $branchId,
                    'product_id' => $product->id,
                    'type'       => 'out',
                    'quantity'   => $item['quantity'],
                    'date'       => now()->toDateString(),
                ]);
            }
            // Update loyalty points if customer exists
            if ($sale->customer_id) {
                $customer = \App\Models\Customer::find($sale->customer_id);
                if ($customer) {
                    $pointsEarned = floor($sale->total);
                    $newPoints = $customer->loyalty_points + $pointsEarned;
                    $tier = 'bronze';
                    if ($newPoints >= 2000) {
                        $tier = 'gold';
                    } elseif ($newPoints >= 500) {
                        $tier = 'silver';
                    }
                    $customer->update([
                        'loyalty_points' => $newPoints,
                        'loyalty_tier'   => $tier,
                    ]);
                }
            }

            DB::commit();
            session()->flash('success', "Sale #{$sale->id} created! Total: \${$total}. Stock deducted for " . count($validated['items']) . " product(s).");
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create sale: ' . $e->getMessage());
            return back()->withInput();
        }

        if ($request->has('no_print') && $request->no_print) {
            return redirect()->route('sales.create');
        }

        return redirect()->route('sales.receipt', ['sale' => $sale->id, 'print' => 1]);
    }

    public function show(Request $request, Sale $sale)
    {
        $sale->load('items.product', 'customer', 'user', 'branch');
        return view('sales.show', compact('sale'));
    }

    public function latestReceipt()
    {
        $sale = Sale::latest()->firstOrFail();
        return redirect()->route('sales.receipt', ['sale' => $sale->id, 'print' => 1]);
    }

    public function receipt(Sale $sale)
    {
        $sale->load('items.product', 'customer', 'user', 'branch');
        return view('sales.receipt', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $customers = Customer::all();
        return view('sales.edit', compact('sale', 'customers'));
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'payment_status' => 'required|string|in:paid,partial,pending',
            'customer_id'    => 'nullable|integer|exists:customers,id',
        ]);

        try {
            $sale->update($validated);
            session()->flash('success', 'Sale updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update sale: ' . $e->getMessage());
            return back()->withInput();
        }

        return redirect()->route('sales.index');
    }

    public function destroy(Sale $sale)
    {
        try {
            DB::beginTransaction();

            // Restore stock for each item before deleting
            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock_quantity', $item->quantity);

                    // Log the reversal
                    InventoryAction::create([
                        'branch_id'  => $sale->branch_id,
                        'product_id' => $product->id,
                        'type'       => 'in',
                        'quantity'   => $item->quantity,
                        'date'       => now()->toDateString(),
                    ]);
                }
            }

            $sale->delete(); // cascade deletes sale_items
            DB::commit();
            session()->flash('success', 'Sale deleted and stock restored!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to delete sale: ' . $e->getMessage());
        }
        return redirect()->route('sales.index');
    }
}
