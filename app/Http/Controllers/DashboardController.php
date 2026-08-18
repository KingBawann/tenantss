<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Date Range Filter
        $range = $request->query('range', 'month'); // today, week, month, all
        
        $startDate = Carbon::today()->startOfMonth();
        $endDate = Carbon::now();
        
        if ($range === 'today') {
            $startDate = Carbon::today();
        } elseif ($range === 'week') {
            $startDate = Carbon::today()->startOfWeek();
        } elseif ($range === 'all') {
            $startDate = Carbon::create(2000, 1, 1);
        }

        // 1. Daily revenue (last 30 days - always show for the chart regardless of filter)
        $thirtyDaysAgo = Carbon::today()->subDays(29);
        $dailyRevenueRaw = Sale::selectRaw('DATE(created_at) as date, SUM(total) as daily_total')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $chartDates = collect();
        $chartRevenue = collect();
        for ($i = 29; $i >= 0; $i--) {
            $dateStr = Carbon::today()->subDays($i)->format('Y-m-d');
            $chartDates->push(Carbon::today()->subDays($i)->format('M d'));
            $match = $dailyRevenueRaw->firstWhere('date', $dateStr);
            $chartRevenue->push($match ? (float)$match->daily_total : 0);
        }

        // 2. Revenue (Filtered vs Previous Period)
        $currentRevenue = Sale::whereBetween('created_at', [$startDate, $endDate])->sum('total');
        
        // Calculate previous period for growth
        $daysDiff = $startDate->diffInDays($endDate) ?: 1;
        $prevStartDate = $startDate->copy()->subDays($daysDiff);
        $prevEndDate = $startDate->copy()->subSeconds(1);
        
        $prevRevenue = Sale::whereBetween('created_at', [$prevStartDate, $prevEndDate])->sum('total');
        
        $revenueGrowth = 0;
        if ($prevRevenue > 0) {
            $revenueGrowth = (($currentRevenue - $prevRevenue) / $prevRevenue) * 100;
        } elseif ($currentRevenue > 0) {
            $revenueGrowth = 100;
        }

        // 3. Transactions & Average Value
        $totalSalesCount = Sale::whereBetween('created_at', [$startDate, $endDate])->count();
        $averageTransactionValue = $totalSalesCount > 0 ? $currentRevenue / $totalSalesCount : 0;

        // 4. Best-Selling Products (by quantity)
        $topSelling = SaleItem::whereHas('sale', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('product')
            ->get();

        // 5. Worst-Selling Products (Products that sold the least > 0)
        $worstSelling = SaleItem::whereHas('sale', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderBy('total_qty', 'asc')
            ->take(5)
            ->with('product')
            ->get();

        // 6. Most Profitable Products (by margin)
        $topProfitable = SaleItem::select('sale_items.product_id', DB::raw('SUM((sale_items.price - products.cost_price) * sale_items.quantity) as total_profit'))
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('sale_items.product_id')
            ->orderByDesc('total_profit')
            ->take(5)
            ->get()
            ->map(function($item) {
                $item->product = Product::withTrashed()->find($item->product_id);
                return $item;
            });

        // 7. Sales by Category (Pie Chart Data)
        $categorySales = SaleItem::select('categories.name', DB::raw('SUM(sale_items.price * sale_items.quantity) as category_total'))
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('categories.name')
            ->orderByDesc('category_total')
            ->get();
            
        // 8. Sales by Payment Method
        $paymentMethods = Sale::select('payment_method', DB::raw('SUM(total) as total_amount'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();

        // 9. Sales by Cashier
        $cashierSales = Sale::select('user_id', DB::raw('SUM(total) as total_amount'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('total_amount')
            ->get();

        // Standard KPIs
        $totalRevenue = Sale::sum('total');
        $totalProducts = Product::count();

        // Low stock products
        $lowStockProducts = Product::with('category')
                                   ->where('stock_quantity', '<=', 10)
                                   ->latest('updated_at')
                                   ->take(5)
                                   ->get();

        // Expiring products
        $thirtyDaysFromNow = now()->addDays(30)->endOfDay();
        $twelveMonthsAgo = now()->subMonths(12)->startOfDay();
        
        $expiringProducts = Product::whereHas('purchaseItems', function ($q) use ($thirtyDaysFromNow, $twelveMonthsAgo) {
                $q->whereNotNull('expiry_date')
                  ->where('expiry_date', '<=', $thirtyDaysFromNow)
                  ->whereHas('purchase', function($q2) use ($twelveMonthsAgo) {
                      $q2->where('created_at', '>=', $twelveMonthsAgo);
                  });
            })
            ->get()
            ->filter(function($p) {
                return $p->expiry_status !== 'good'; 
            })
            ->sortBy('earliest_expiry_date')
            ->take(5);

        return view('dashboard', compact(
            'range',
            'chartDates',
            'chartRevenue',
            'currentRevenue',
            'prevRevenue',
            'revenueGrowth',
            'averageTransactionValue',
            'topSelling',
            'worstSelling',
            'topProfitable',
            'categorySales',
            'paymentMethods',
            'cashierSales',
            'totalRevenue',
            'totalProducts',
            'totalSalesCount',
            'lowStockProducts',
            'expiringProducts'
        ));
    }
}
