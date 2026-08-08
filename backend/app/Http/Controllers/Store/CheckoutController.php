<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\StorePaymentMethod;
use App\Models\Store;
use App\Services\Payments\PaymentGatewayFactory;
use App\Services\Shipping\ShippingQuoteService;
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
    public function review(Request $request, ShippingQuoteService $shippingQuotes)
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

        $store = Store::find($address->store_id);

        $shippingOptions = $shippingQuotes->quote(
            $store,
            $address->zipcode,
            $address->state,
            collect($cart)
        );

        // Se o cliente já tinha escolhido uma opção de frete ao
        // simular no carrinho (antes de logar), e o CEP bate com o
        // endereço escolhido agora, pré-seleciona a mesma opção.
        $preSelectedIndex = 0;

        $savedSelection = session('shipping_selection');

        if ($savedSelection && preg_replace('/\D/', '', $address->zipcode) === $savedSelection['zipcode']) {

            $index = collect($shippingOptions)->search(function ($opt) use ($savedSelection) {
                return $opt['provider'] === $savedSelection['provider']
                    && $opt['name'] === $savedSelection['name'];
            });

            if ($index !== false) {
                $preSelectedIndex = $index;
            }
        }

        return view('store.checkout.review', compact('address', 'cart', 'cartTotal', 'shippingOptions', 'preSelectedIndex'));
    }

    /**
     * Passo 3: cria o pedido de fato (status "pending" — aguardando
     * pagamento). O frete escolhido é RECALCULADO aqui no servidor
     * (nunca confiamos no preço que veio do formulário) para evitar
     * que alguém manipule o valor no navegador.
     */
    public function placeOrder(Request $request, TenantManager $tenant, ShippingQuoteService $shippingQuotes)
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

        $request->validate([
            'shipping_choice' => 'required|string',
        ]);

        [$shippingProvider, $shippingName] = array_pad(
            explode('|', $request->input('shipping_choice'), 2),
            2,
            null
        );

        $store = Store::find($tenant->id());

        $availableOptions = $shippingQuotes->quote(
            $store,
            $address->zipcode,
            $address->state,
            collect($cart)
        );

        $chosenOption = collect($availableOptions)->first(
            fn ($opt) => $opt['provider'] === $shippingProvider && $opt['name'] === $shippingName
        );

        if (! $chosenOption) {
            return back()->with('error', 'Opção de frete inválida ou não disponível mais. Revise o pedido.');
        }

        $order = DB::transaction(function () use ($cart, $customer, $address, $tenant, $chosenOption) {

            $subtotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['qty']);

            $shippingCost = (float) $chosenOption['price'];

            $order = Order::create([
                'store_id' => $tenant->id(),
                'customer_id' => $customer->id,
                'customer_address_id' => $address->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'shipping_method_name' => $chosenOption['name'],
                'total' => $subtotal + $shippingCost,
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
        session()->forget('shipping_selection');

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

        // Checkout Pro (e qualquer gateway futuro no mesmo estilo) não
        // usa a nossa tela de pagamento — o cliente precisa ir direto
        // pro ambiente do provedor. Sem isso, ele caía sempre na tela
        // de PIX (pay.blade.php), que fica vazia pra esse tipo de
        // cobrança porque não existe copy_paste/pix_key na resposta.
        if (! empty($chargeData['redirect_url'])) {
            return redirect()->away($chargeData['redirect_url']);
        }

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
     * Endpoint leve pra tela de pagamento consultar (via JS,
     * periodicamente) se o pedido já foi confirmado — usado pelos
     * gateways com confirmação automática (Mercado Pago, etc.), que
     * não dependem do cliente clicar em nada.
     */
    public function paymentStatus(Order $order)
    {
        $customer = Auth::guard('customer')->user();

        abort_if($order->customer_id !== $customer->id, 403);

        return response()->json([
            'status' => $order->status,
        ]);
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

    /**
     * Simula o frete a partir do carrinho da sessão, sem exigir
     * login nem endereço cadastrado — só o CEP e a UF (a UF o
     * próprio front descobre via ViaCEP antes de chamar isso).
     * Pensado pra tela do carrinho, onde o cliente ainda não logou.
     */
    public function simulateShipping(Request $request, TenantManager $tenant, ShippingQuoteService $shippingQuotes)
    {
        $request->validate([
            'zipcode' => 'required|string',
            'state' => 'required|string|size:2',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Seu carrinho está vazio.',
            ]);
        }

        $store = Store::find($tenant->id());

        if (! $store) {
            return response()->json([
                'success' => false,
                'message' => 'Loja não encontrada.',
            ]);
        }

        $options = $shippingQuotes->quote(
            $store,
            $request->zipcode,
            strtoupper($request->state),
            collect($cart)
        );

        return response()->json([
            'success' => true,
            'options' => $options,
        ]);
    }

    /**
     * Guarda na sessão qual opção de frete o cliente escolheu ao
     * simular no carrinho (ainda sem login). Quando ele chegar na
     * revisão do pedido (depois de logar ou clicar em "Comprar
     * Agora"), essa escolha é usada para pré-selecionar a mesma
     * opção, se o CEP bater com o endereço escolhido.
     */
    public function selectShipping(Request $request)
    {
        $request->validate([
            'zipcode' => 'required|string',
            'provider' => 'required|string',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'estimated_days' => 'nullable|integer',
        ]);

        session()->put('shipping_selection', [
            'zipcode' => preg_replace('/\D/', '', $request->zipcode),
            'provider' => $request->provider,
            'name' => $request->name,
            'price' => (float) $request->price,
            'estimated_days' => $request->estimated_days,
        ]);

        return response()->json(['success' => true]);
    }
}
