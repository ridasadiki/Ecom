<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalUsers = User::where('role', 'customer')->count();
        
        $salesData = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_price) as total_sales'),
            DB::raw('COUNT(*) as order_count')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $categoryData = Category::withCount('products')
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'count' => $category->products_count
                ];
            });

        $customerTrend = User::select(
            DB::raw('DATE(DATE_SUB(created_at, INTERVAL WEEKDAY(created_at) DAY)) as week_start'),
            DB::raw('COUNT(*) as count')
        )
            ->where('role', 'customer')
            ->where('created_at', '>=', Carbon::now()->subWeeks(6))
            ->groupBy('week_start')
            ->orderBy('week_start')
            ->get()
            ->map(function ($item) {
                $weekStart = Carbon::parse($item->week_start);
                return [
                    'week' => $weekStart->format('M d') . ' - ' . $weekStart->copy()->addDays(6)->format('M d'),
                    'count' => $item->count
                ];
            });

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalUsers',
            'salesData',
            'categoryData',
            'customerTrend'
        ));
    }
}
