<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
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

        $payment = Payment::where('order_id', $order->id)->latest()->first();

        return view('admin.orders.show', compact('order', 'payment'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        abort_if(
            $order->store_id != auth()->user()->store_id,
            403
        );

        $request->validate([
            'status' => 'required|in:pending,awaiting_confirmation,paid,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status do pedido atualizado.');
    }

    /**
     * Confirma o recebimento do pagamento (PIX manual) — o lojista
     * conferiu no extrato do próprio banco e está confirmando aqui.
     * Marca o Payment mais recente como confirmado e o pedido como pago.
     */
    public function confirmPayment(Order $order)
    {
        abort_if(
            $order->store_id != auth()->user()->store_id,
            403
        );

        $payment = Payment::where('order_id', $order->id)->latest()->first();

        if ($payment) {
            $payment->update([
                'status' => 'confirmed',
                'confirmed_by' => 'admin',
                'confirmed_at' => now(),
            ]);
        }

        $order->update(['status' => 'paid']);

        return back()->with('success', 'Pagamento confirmado! Pedido marcado como pago.');
    }
}
