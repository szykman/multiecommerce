<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\StorePaymentMethod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Integração real com a API de Pagamentos do Mercado Pago (PIX
 * dinâmico via payment_method_id=pix). Usa chamadas HTTP diretas
 * (Illuminate\Http\Client), sem o SDK oficial deles — mesmo padrão
 * usado no CorreiosShippingCalculator, pra manter consistência e
 * não adicionar dependência nova no composer.
 *
 * Fica tudo dentro da própria loja (o cliente nunca sai pro domínio
 * do Mercado Pago) — legítimo porque PIX não envolve dado sensível
 * de pagamento (diferente de cartão, que exigiria tokenização via
 * Bricks/Checkout Transparente antes de chegar no backend).
 *
 * Confirmação é AUTOMÁTICA, via webhook (ver
 * app/Http/Controllers/Webhooks/MercadoPagoWebhookController).
 */
class MercadoPagoPixGateway implements PaymentGatewayInterface
{
    protected const API_URL = 'https://api.mercadopago.com/v1/payments';

    public function charge(Order $order, StorePaymentMethod $method): array
    {
        $credentials = $method->credentials ?? [];

        $accessToken = $credentials['access_token'] ?? null;

        if (! $accessToken) {
            throw new \RuntimeException('Mercado Pago não está configurado corretamente para esta loja.');
        }

        $customer = $order->customer;

        $payload = [
            'transaction_amount' => (float) $order->total,
            'description' => 'Pedido #'.$order->id,
            'payment_method_id' => 'pix',
            'external_reference' => (string) $order->id,
            'notification_url' => route('webhooks.mercadopago', ['store_id' => $order->store_id]),
            'payer' => [
                'email' => $customer->email,
                'first_name' => $customer->name,
            ],
        ];

        try {

            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'X-Idempotency-Key' => (string) Str::uuid(),
                ])
                ->post(self::API_URL, $payload);

            if (! $response->successful()) {

                Log::warning('Mercado Pago - resposta não OK ao criar pagamento', [
                    'order_id' => $order->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \RuntimeException('Não foi possível gerar a cobrança PIX no Mercado Pago.');
            }

            $data = $response->json();

            $transactionData = $data['point_of_interaction']['transaction_data'] ?? [];

            return [
                'provider' => 'mercadopago',
                'txid' => (string) ($data['id'] ?? ''),
                'copy_paste' => $transactionData['qr_code'] ?? '',
                'qr_base64' => $transactionData['qr_code_base64'] ?? null,
                'pix_key' => 'Mercado Pago',
                'holder_name' => $order->store->name ?? '',
                'amount' => $order->total,
            ];

        } catch (\RuntimeException $e) {

            throw $e;

        } catch (\Throwable $e) {

            Log::warning('Erro ao gerar cobrança Mercado Pago', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Erro ao se comunicar com o Mercado Pago.');
        }
    }
}
