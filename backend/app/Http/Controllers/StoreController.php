<?php

namespace App\Http\Controllers;

use App\Models\Product;

class StoreController extends Controller
{
    public function index()
    {
        $products = Product::where('active',1)
            ->latest()
            ->get();

        return view('store.home', compact('products'));
    }
}
