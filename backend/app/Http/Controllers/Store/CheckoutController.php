<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\StorePaymentMethod;
use App\Services\Payments\PaymentGatewayFactory;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Passo 1: escolher ou cadastrar um endereço de entrega.
     */
    public function address()
    {
        $customer = Auth::guard('customer')->user();

        $addresses = Address::where('customer_id', $customer->id)
            ->orderByDesc('is_default')
            ->get();

        return view('store.checkout.address', compact('addresses'));
    }

    public function storeAddress(Request $request, TenantManager $tenant)
    {
        $data = $request->validate([
            'label' => 'nullable|string|max:60',
            'recipient_name' => 'required|string|max:255',
            'zipcode' => 'required|string|max:9',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:100',
            'neighborhood' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|size:2',
        ]);

        $customer = Auth::guard('customer')->user();

        $isDefault = $request->boolean('is_default')
            || Address::where('customer_id', $customer->id)->count() === 0;

        if ($isDefault) {
            Address::where('customer_id', $customer->id)->update(['is_default' => false]);
        }

        Address::create(array_merge($data, [
            'customer_id' => $customer->id,
            'store_id' => $tenant->id(),
            'is_default' => $isDefault,
        ]));

        if ($request->input('context') === 'account') {
            return redirect()
                ->route('store.addresses')
                ->with('success', 'Endereço cadastrado.');
        }

        return redirect()
            ->route('store.checkout.review')
            ->with('success', 'Endereço salvo.');
    }

    /**
     * Passo 2: revisão do carrinho + endereço escolhido antes de
     * confirmar o pedido.
     */
    public function review(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $addressId = $request->query('address_id');

        $address = $addressId
            ? Address::where('customer_id', $customer->id)->find($addressId)
            : Address::where('customer_id', $customer->id)->where('is_default', true)->first();

        if (! $address) {
            $address = Address::where('customer_id', $customer->id)->first();
        }

        if (! $address) {
            return redirect()
                ->route('store.checkout.address')
                ->with('error', 'Cadastre um endereço de entrega para continuar.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('store.cart')
                ->with('error', 'Seu carrinho está vazio.');
        }

        $cartTotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['qty']);

        return view('store.checkout.review', compact('address', 'cart', 'cartTotal'));
    }

    /**
     * Passo 3: cria o pedido de fato (status "pending" — aguardando
     * pagamento). O PIX/pagamento é um sprint futuro; aqui só
     * fechamos o pedido e limpamos o carrinho.
     */
    public function placeOrder(Request $request, TenantManager $tenant)
    {
        $customer = Auth::guard('customer')->user();

        $address = Address::where('customer_id', $customer->id)
            ->find($request->input('address_id'));

        if (! $address) {
            return back()->with('error', 'Endereço inválido.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('store.cart')
                ->with('error', 'Seu carrinho está vazio.');
        }

        $order = DB::transaction(function () use ($cart, $customer, $address, $tenant) {

            $subtotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['qty']);

            $order = Order::create([
                'store_id' => $tenant->id(),
                'customer_id' => $customer->id,
                'customer_address_id' => $address->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => 0,
                'total' => $subtotal,
                'address_snapshot' => $address->only([
                    'label', 'recipient_name', 'zipcode', 'street',
                    'number', 'complement', 'neighborhood', 'city', 'state',
                ]),
            ]);

            foreach ($cart as $item) {

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['price'] * $item['qty'],
                ]);
            }

            return $order;
        });

        session()->forget('cart');

        return redirect()
            ->route('store.checkout.payment', $order)
            ->with('success', 'Pedido criado! Falta escolher a forma de pagamento.');
    }

    /**
     * Passo 4: escolher a forma de pagamento habilitada pela loja.
     */
    public function choosePayment(Order $order)
    {
        $customer = Auth::guard('customer')->user();

        abort_if($order->customer_id !== $customer->id, 403);

        $methods = StorePaymentMethod::where('store_id', $order->store_id)
            ->where('enabled', true)
            ->orderBy('position')
            ->get();

        return view('store.checkout.payment', compact('order', 'methods'));
    }

    /**
     * Gera a cobrança no gateway escolhido e manda para a tela de
     * pagamento (QR/copia-e-cola no caso do PIX manual).
     */
    public function selectPayment(Request $request, Order $order)
    {
        $customer = Auth::guard('customer')->user();

        abort_if($order->customer_id !== $customer->id, 403);

        $request->validate([
            'provider' => 'required|string',
        ]);

        $method = StorePaymentMethod::where('store_id', $order->store_id)
            ->where('provider', $request->provider)
            ->where('enabled', true)
            ->first();

        if (! $method) {
            return back()->with('error', 'Forma de pagamento indisponível.');
        }

        try {

            $gateway = PaymentGatewayFactory::make($method->provider);
            $chargeData = $gateway->charge($order, $method);

        } catch (\Throwable $e) {

            return back()->with('error', 'Não foi possível gerar a cobrança: '.$e->getMessage());
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => $method->provider,
            'reference' => $chargeData['txid'] ?? null,
            'amount' => $order->total,
            'status' => 'pending',
            'raw_response' => $chargeData,
        ]);

        $order->update(['payment_method' => $method->provider]);

        return redirect()->route('store.checkout.pay', $order);
    }

    /**
     * Tela de pagamento (QR + copia-e-cola no PIX manual) + botão
     * "Já paguei".
     */
    public function showPayment(Order $order)
    {
        $customer = Auth::guard('customer')->user();

        abort_if($order->customer_id !== $customer->id, 403);

        $payment = Payment::where('order_id', $order->id)
            ->latest()
            ->first();

        if (! $payment) {
            return redirect()->route('store.checkout.payment', $order);
        }

        return view('store.checkout.pay', compact('order', 'payment'));
    }

    /**
     * O cliente clica "Já paguei" — auto-declaração, ainda precisa
     * o lojista conferir e confirmar manualmente no admin.
     */
    public function confirmPaidByCustomer(Order $order)
    {
        $customer = Auth::guard('customer')->user();

        abort_if($order->customer_id !== $customer->id, 403);

        $payment = Payment::where('order_id', $order->id)->latest()->first();

        if ($payment && $payment->status === 'pending') {

            $payment->update([
                'status' => 'awaiting_confirmation',
                'confirmed_by' => 'customer',
            ]);

            $order->update(['status' => 'awaiting_confirmation']);
        }

        return redirect()
            ->route('store.checkout.confirmation', $order)
            ->with('success', 'Avisamos a loja! Assim que confirmarem o recebimento, seu pedido será processado.');
    }

    public function confirmation(Order $order)
    {
        $customer = Auth::guard('customer')->user();

        abort_if($order->customer_id !== $customer->id, 403);

        return view('store.checkout.confirmation', compact('order'));
    }

    /**
     * Histórico de pedidos do cliente.
     */
    public function myOrders()
    {
        $customer = Auth::guard('customer')->user();

        $orders = Order::where('customer_id', $customer->id)
            ->latest()
            ->get();

        return view('store.checkout.orders', compact('orders'));
    }
}
