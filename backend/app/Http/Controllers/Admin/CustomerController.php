<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::where(
                'store_id',
                auth()->user()->store_id
            )
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%')
                      ->orWhere('email', 'like', '%'.$request->search.'%');
                });
            })
            ->withCount('orders')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        abort_if(
            $customer->store_id != auth()->user()->store_id,
            403
        );

        $customer->load(['addresses', 'orders' => function ($q) {
            $q->latest();
        }]);

        return view('admin.customers.show', compact('customer'));
    }
}
