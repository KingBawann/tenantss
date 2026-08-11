<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function zReport(Request $request)
    {
        $user = Auth::user();
        
        // Base query for today's sales that are completed (paid or partial)
        // Adjust status filter if you want to include pending.
        $query = Sale::whereDate('created_at', today())
                     ->whereIn('payment_status', ['paid', 'partial']);

        // If the user is NOT an admin, restrict the query to only their own sales
        if (!$user->hasAdminAccess()) {
            $query->where('user_id', $user->id);
        }

        // Group by user and payment method
        $salesData = $query->select(
            'user_id',
            'payment_method',
            DB::raw('SUM(total) as total_amount')
        )
        ->groupBy('user_id', 'payment_method')
        ->get();

        // Organize the data for the view
        $reports = [];

        foreach ($salesData as $row) {
            $userId = $row->user_id;
            
            if (!isset($reports[$userId])) {
                $reports[$userId] = [
                    'user'  => User::find($userId),
                    'cash'  => 0,
                    'card'  => 0,
                    'total' => 0,
                ];
            }

            if ($row->payment_method === 'cash') {
                $reports[$userId]['cash'] = $row->total_amount;
            } elseif ($row->payment_method === 'card') {
                $reports[$userId]['card'] = $row->total_amount;
            }

            $reports[$userId]['total'] += $row->total_amount;
        }

        return view('reports.z-report', compact('reports'));
    }

    public function profitAndLoss(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Start of day for start date, end of day for end date
        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();

        // 1. Gross Sales (Revenue from sales)
        $grossSales = Sale::whereBetween('created_at', [$start, $end])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->get()
            ->sum(function($sale) {
                return $sale->discountedTotal; // Taking discounts into account
            });

        // 2. Cost of Goods Sold (Sales COGS)
        // Fetch sale items within the date range
        $saleItems = \App\Models\SaleItem::with('product')
            ->whereHas('sale', function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                  ->whereIn('payment_status', ['paid', 'partial']);
            })->get();
            
        $cogsSales = $saleItems->sum(function($item) {
            return $item->quantity * ($item->product->cost_price ?? 0);
        });

        // 3. Sales Returns (Refunds)
        $returns = \App\Models\SaleReturn::whereBetween('created_at', [$start, $end])->get();
        $totalRefunds = $returns->sum('total');

        // 4. Cost of Goods Returned
        $returnItems = \App\Models\SaleReturnItem::with('product')
            ->whereHas('saleReturn', function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            })->get();
            
        $cogsReturns = $returnItems->sum(function($item) {
            return $item->quantity * ($item->product->cost_price ?? 0);
        });

        // Calculations
        $netSales = $grossSales - $totalRefunds;
        $netCogs = $cogsSales - $cogsReturns;
        $grossProfit = $netSales - $netCogs;
        $marginPercent = $netSales > 0 ? ($grossProfit / $netSales) * 100 : 0;

        return view('reports.profit-loss', compact(
            'startDate',
            'endDate',
            'grossSales',
            'cogsSales',
            'totalRefunds',
            'cogsReturns',
            'netSales',
            'netCogs',
            'grossProfit',
            'marginPercent'
        ));
    }

    public function inventory(Request $request)
    {
        $lowStockThreshold = $request->get('threshold', 10);
        $thirtyDaysAgo = now()->subDays(30);

        // Fetch products with sales count in the last 30 days
        $products = \App\Models\Product::with('category')
            ->withCount(['saleItems' => function($query) use ($thirtyDaysAgo) {
                $query->whereHas('sale', function($q) use ($thirtyDaysAgo) {
                    $q->where('created_at', '>=', $thirtyDaysAgo)
                      ->whereIn('payment_status', ['paid', 'partial']);
                });
            }])
            ->orderBy('name')
            ->get();

        // Calculate aggregate metrics
        $totalItems = $products->sum('stock_quantity');
        $totalCostValue = $products->sum(function($p) { return $p->stock_quantity * $p->cost_price; });
        $totalRetailValue = $products->sum(function($p) { return $p->stock_quantity * $p->price; });
        
        $lowStockProducts = $products->filter(function($p) use ($lowStockThreshold) {
            return $p->stock_quantity > 0 && $p->stock_quantity <= $lowStockThreshold;
        });
        $outOfStockProducts = $products->where('stock_quantity', 0);
        $deadStockProducts = $products->filter(function($p) {
            return $p->sale_items_count === 0 && $p->stock_quantity > 0;
        });

        // Handle CSV Export
        if ($request->get('export') === 'csv') {
            $filename = "inventory_report_" . date('Y-m-d') . ".csv";
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use ($products) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Barcode', 'Name', 'Category', 'Stock Qty', 'Cost Price', 'Retail Price', 'Total Cost Val', 'Total Retail Val', 'Sales (Last 30 Days)', 'Status']);

                foreach ($products as $p) {
                    $status = 'Healthy';
                    if ($p->stock_quantity == 0) $status = 'Out of Stock';
                    elseif ($p->stock_quantity <= 10) $status = 'Low Stock';
                    if ($p->sale_items_count == 0 && $p->stock_quantity > 0) $status = 'Dead Stock';

                    fputcsv($file, [
                        $p->barcode,
                        $p->name,
                        $p->category->name ?? 'Uncategorized',
                        $p->stock_quantity,
                        number_format($p->cost_price, 2, '.', ''),
                        number_format($p->price, 2, '.', ''),
                        number_format($p->stock_quantity * $p->cost_price, 2, '.', ''),
                        number_format($p->stock_quantity * $p->price, 2, '.', ''),
                        $p->sale_items_count,
                        $status
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('reports.inventory', compact(
            'products',
            'totalItems',
            'totalCostValue',
            'totalRetailValue',
            'lowStockProducts',
            'outOfStockProducts',
            'deadStockProducts',
            'lowStockThreshold'
        ));
    }
}
