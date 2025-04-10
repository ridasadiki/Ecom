<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $orders = Order::with('product')->get();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::all();
        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'client_name' => 'required|string|max:255',
            'client_address' => 'required|string|max:255',
            'client_phone' => 'required|string|max:15',
        ]);

        $product = Product::find($request->product_id);
        $total_price = $product->price * $request->quantity;

        // Create the order with the new fields
        Order::create([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_price' => $total_price,
            'is_delivered' => false, // Default to not delivered
            'client_name' => $request->client_name,
            'client_address' => $request->client_address,
            'client_phone' => $request->client_phone,
        ]);

        return redirect()->route('orders.index')->with('success', 'Order placed successfully!');
    }


    public function edit(Order $order)
    {
        $products = Product::all();
        return view('orders.edit', compact('order', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'client_name' => 'required|string|max:255',
            'client_address' => 'required|string|max:255',
            'client_phone' => 'required|string|max:15',
            'is_delivered' => 'nullable|boolean', // Allow marking as delivered or not
        ]);

        $product = Product::find($request->product_id);
        $total_price = $product->price * $request->quantity;

        $order->update([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_price' => $total_price,
            'is_delivered' => $request->is_delivered, // Update delivery status
            'client_name' => $request->client_name,
            'client_address' => $request->client_address,
            'client_phone' => $request->client_phone,
        ]);

        return redirect()->route('orders.index')->with('success', 'Order updated successfully!');
    }


    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
    }
    public function updateDeliveryStatus(Request $request, Order $order)
    {
        // Validate the input
        $request->validate([
            'is_delivered' => 'required|boolean',
        ]);

        // Update the delivery status
        $order->update([
            'is_delivered' => $request->is_delivered,
        ]);

        // Return a JSON response to confirm the update
        return response()->json(['success' => true]);
    }
}
