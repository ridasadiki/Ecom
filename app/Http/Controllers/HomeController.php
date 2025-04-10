<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $categoriesCount = Category::count();  
        $productsCount = Product::count();    
        $ordersCount = Order::count();         

    return view('dashboard', compact('categoriesCount', 'productsCount', 'ordersCount'));
    }
}
