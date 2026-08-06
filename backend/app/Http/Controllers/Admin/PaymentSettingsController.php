<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StorePaymentMethod;
use Illuminate\Http\Request;

class PaymentSettingsController extends Controller
{
    /**
     * Os 3 "sub-métodos" do Mercado Pago que compartilham o mesmo
     * Access Token, mas aparecem como opções separadas pro cliente.
     */
    protected array $mercadoPagoProviders = [
        'mercadopago_pix',
        'mercadopago_boleto',
        'mercadopago_checkout_pro',
    ];

    public function edit()
    {
        $storeId = auth()->user()->store_id;

        $pixMethod = StorePaymentMethod::firstOrNew([
            'store_id' => $storeId,
            'provider' => 'pix_manual',
        ]);

        // Usa o primeiro registro do Mercado Pago que existir só pra
        // pegar o Access Token já salvo (é o mesmo pra todos os 3)
        $mercadoPagoMethod = StorePaymentMethod::where('store_id', $storeId)
            ->whereIn('provider', $this->mercadoPagoProviders)
            ->first() ?? new StorePaymentMethod();

        $mpPixEnabled = StorePaymentMethod::where('store_id', $storeId)
            ->where('provider', 'mercadopago_pix')->value('enabled') ?? false;

        $mpBoletoEnabled = StorePaymentMethod::where('store_id', $storeId)
            ->where('provider', 'mercadopago_boleto')->value('enabled') ?? false;

        $mpCheckoutProEnabled = StorePaymentMethod::where('store_id', $storeId)
            ->where('provider', 'mercadopago_checkout_pro')->value('enabled') ?? false;

        return view('admin.payment_settings.edit', compact(
            'pixMethod',
            'mercadoPagoMethod',
            'mpPixEnabled',
            'mpBoletoEnabled',
            'mpCheckoutProEnabled'
        ));
    }

    public function update(Request $request)
    {
        $storeId = auth()->user()->store_id;

        $data = $request->validate([
            'pix_key' => 'required|string|max:140',
            'pix_key_type' => 'required|in:cpf,cnpj,email,phone,random',
            'holder_name' => 'required|string|max:100',
            'city' => 'required|string|max:60',
        ]);

        StorePaymentMethod::updateOrCreate(
            ['store_id' => $storeId, 'provider' => 'pix_manual'],
            [
                'enabled' => $request->boolean('enabled'),
                'credentials' => [
                    'pix_key' => $data['pix_key'],
                    'pix_key_type' => $data['pix_key_type'],
                    'holder_name' => $data['holder_name'],
                    'city' => $data['city'],
                ],
            ]
        );

        return back()->with('success', 'Configuração de pagamento salva.');
    }

    /**
     * Salva o Access Token do Mercado Pago (um só, compartilhado)
     * e grava/atualiza os 3 registros (PIX, Boleto, Checkout Pro),
     * cada um podendo ser ligado/desligado independentemente.
     */
    public function updateMercadoPago(Request $request)
    {
        $storeId = auth()->user()->store_id;

        $data = $request->validate([
            'mp_access_token' => 'required|string|max:255',
            'mp_public_key' => 'nullable|string|max:255',
        ]);

        $credentials = [
            'access_token' => $data['mp_access_token'],
            'public_key' => $data['mp_public_key'] ?? null,
        ];

        $enabledMap = [
            'mercadopago_pix' => $request->boolean('mp_enable_pix'),
            'mercadopago_boleto' => $request->boolean('mp_enable_boleto'),
            'mercadopago_checkout_pro' => $request->boolean('mp_enable_checkout_pro'),
        ];

        foreach ($enabledMap as $provider => $enabled) {

            StorePaymentMethod::updateOrCreate(
                ['store_id' => $storeId, 'provider' => $provider],
                [
                    'enabled' => $enabled,
                    'credentials' => $credentials,
                ]
            );
        }

        return back()->with('success', 'Configuração do Mercado Pago salva.');
    }
}
