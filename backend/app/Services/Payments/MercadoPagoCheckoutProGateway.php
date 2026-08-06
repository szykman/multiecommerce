<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\StorePaymentMethod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Checkout Pro do Mercado Pago — o cliente é redirecionado pro
 * ambiente seguro deles, onde pode pagar com cartão, boleto, PIX,
 * saldo em conta, etc. Zero dado de pagamento passa pelo nosso
 * servidor. Usada como a opção "Cartões e outros pagamentos" no
 * checkout, ao lado do PIX e Boleto que ficam dentro da loja.
 */
class MercadoPagoCheckoutProGateway implements PaymentGatewayInterface
{
    protected const PREFERENCE_URL = 'https://api.mercadopago.com/checkout/preferences';

    public function charge(Order $order, StorePaymentMethod $method): array
    {
        $credentials = $method->credentials ?? [];

        $accessToken = $credentials['access_token'] ?? null;

        if (! $accessToken) {
            throw new \RuntimeException('Mercado Pago não está configurado corretamente para esta loja.');
        }

        $customer = $order->customer;

        $payload = [
            'items' => [
                [
                    'title' => 'Pedido #'.$order->id,
                    'quantity' => 1,
                    'unit_price' => (float) $order->total,
                    'currency_id' => 'BRL',
                ],
            ],
            'payer' => [
                'email' => $customer->email,
                'name' => $customer->name,
            ],
            'back_urls' => [
                'success' => route('store.checkout.confirmation', $order),
                'failure' => route('store.checkout.payment', $order),
                'pending' => route('store.checkout.confirmation', $order),
            ],
            'auto_return' => 'approved',
            'notification_url' => route('webhooks.mercadopago', ['store_id' => $order->store_id]),
            'external_reference' => (string) $order->id,
        ];

        try {

            $response = Http::withToken($accessToken)
                ->post(self::PREFERENCE_URL, $payload);

            if (! $response->successful()) {

                Log::warning('Mercado Pago Checkout Pro - resposta não OK', [
                    'order_id' => $order->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \RuntimeException('Não foi possível iniciar o pagamento no Mercado Pago.');
            }

            $data = $response->json();

            $isTestCredential = str_starts_with($accessToken, 'TEST-');

            $redirectUrl = $isTestCredential
                ? ($data['sandbox_init_point'] ?? $data['init_point'] ?? null)
                : ($data['init_point'] ?? null);

            if (! $redirectUrl) {
                throw new \RuntimeException('Mercado Pago não devolveu um link de pagamento válido.');
            }

            return [
                'provider' => 'mercadopago_checkout_pro',
                'txid' => $data['id'] ?? null, // ID da preferência (não é o payment id ainda)
                'redirect_url' => $redirectUrl,
                'amount' => $order->total,
            ];

        } catch (\RuntimeException $e) {

            throw $e;

        } catch (\Throwable $e) {

            Log::warning('Erro ao criar preferência Mercado Pago', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Erro ao se comunicar com o Mercado Pago.');
        }
    }
}
