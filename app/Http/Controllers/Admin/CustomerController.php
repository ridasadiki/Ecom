<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')->latest()->paginate(10);
        return view('admin.customers.index', compact('customers'));
    }

    public function show($id)
    {
        $user = User::where('role', 'customer')->findOrFail($id);
        return view('admin.customers.show', compact('user'));
    }
}
