<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Daily revenue (last 30 days)
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

        // 2. Today vs Yesterday Comparison
        $todayRevenue = Sale::whereDate('created_at', Carbon::today())->sum('total');
        $yesterdayRevenue = Sale::whereDate('created_at', Carbon::yesterday())->sum('total');
        
        $revenueGrowth = 0;
        if ($yesterdayRevenue > 0) {
            $revenueGrowth = (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100;
        } elseif ($todayRevenue > 0) {
            $revenueGrowth = 100;
        }

        // 3. Top 5 Best-Selling Products (by quantity)
        $topSelling = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('product')
            ->get();

        // 4. Top 5 Most Profitable Products (by margin)
        $topProfitable = SaleItem::select('sale_items.product_id', DB::raw('SUM((sale_items.price - products.cost_price) * sale_items.quantity) as total_profit'))
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->groupBy('sale_items.product_id')
            ->orderByDesc('total_profit')
            ->take(5)
            ->get()
            ->map(function($item) {
                $item->product = Product::withTrashed()->find($item->product_id);
                return $item;
            });

        // 5. Sales by Category (Pie Chart Data)
        $categorySales = SaleItem::select('categories.name', DB::raw('SUM(sale_items.price * sale_items.quantity) as category_total'))
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->orderByDesc('category_total')
            ->get();

        // Standard KPIs (Keep some existing ones for top row)
        $totalRevenue = Sale::sum('total');
        $totalProducts = Product::count();
        $totalSalesCount = Sale::count();

        // Low stock products
        $lowStockProducts = Product::with('category')
                                   ->where('stock_quantity', '<=', 10)
                                   ->latest('updated_at')
                                   ->take(5)
                                   ->get();

        // Expiring products (approximation by finding those with 'expiring_soon' or 'expired' status)
        // Since getExpiryStatusAttribute is not a database column, we need to load products 
        // that have a recent purchase item with an expiry date within 30 days.
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
                return $p->expiry_status !== 'good'; // either 'expired' or 'expiring_soon'
            })
            ->sortBy('earliest_expiry_date')
            ->take(5);

        return view('dashboard', compact(
            'chartDates',
            'chartRevenue',
            'todayRevenue',
            'yesterdayRevenue',
            'revenueGrowth',
            'topSelling',
            'topProfitable',
            'categorySales',
            'totalRevenue',
            'totalProducts',
            'totalSalesCount',
            'lowStockProducts',
            'expiringProducts'
        ));
    }
}
