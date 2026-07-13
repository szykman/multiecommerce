<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{

public function index()
{
    $store = auth()->user()->store_id;

    $products = \App\Models\Product::where(
        'store_id',
        $store
    )
    ->whereHas('category',function($q){

        $q->where('type','store');

    })
    ->count();

    $pages = \App\Models\Product::where(
        'store_id',
        $store
    )
    ->whereHas('category',function($q){

        $q->where('type','cms');

    })
    ->count();

    $storeCategories = \App\Models\Category::where(
        'store_id',
        $store
    )
    ->where('type','store')
    ->count();

    $cmsCategories = \App\Models\Category::where(
        'store_id',
        $store
    )
    ->where('type','cms')
    ->count();

    return view(
        'admin.dashboard',
        compact(
            'products',
            'pages',
            'storeCategories',
            'cmsCategories'
        )
    );
}

}
