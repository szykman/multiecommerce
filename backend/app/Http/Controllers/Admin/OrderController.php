<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where(
                'store_id',
                auth()->user()->store_id
            )
            ->with('customer')
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if(
            $order->store_id != auth()->user()->store_id,
            403
        );

        $order->load(['items', 'customer']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        abort_if(
            $order->store_id != auth()->user()->store_id,
            403
        );

        $request->validate([
            'status' => 'required|in:pending,paid,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status do pedido atualizado.');
    }
}
