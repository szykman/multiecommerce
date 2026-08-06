<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\StorePaymentMethod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Boleto via Mercado Pago, também direto pela API de Pagamentos
 * (mesmo padrão do PIX) — fica dentro da loja, sem redirecionamento.
 *
 * Diferente do PIX: boleto EXIGE CPF do pagador (identification).
 * Usamos o campo "document" do Customer; se estiver vazio, a
 * cobrança falha com uma mensagem clara pedindo pra completar o
 * cadastro antes de continuar.
 *
 * IMPORTANTE: os nomes de campo da resposta (barcode, external_resource_url,
 * etc.) têm fallback para variações — ainda não testamos contra uma
 * resposta real. Se o link do boleto não aparecer certo mesmo com a
 * chamada retornando 200, cole a resposta completa que a gente ajusta.
 */
class MercadoPagoBoletoGateway implements PaymentGatewayInterface
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

        $document = preg_replace('/\D/', '', $customer->document ?? '');

        if (! $document) {
            throw new \RuntimeException('Para pagar com boleto, complete seu CPF em "Minha Conta" antes de continuar.');
        }

        $payload = [
            'transaction_amount' => (float) $order->total,
            'description' => 'Pedido #'.$order->id,
            'payment_method_id' => 'bolbradesco',
            'external_reference' => (string) $order->id,
            'notification_url' => route('webhooks.mercadopago', ['store_id' => $order->store_id]),
            'payer' => [
                'email' => $customer->email,
                'first_name' => $customer->name,
                'identification' => [
                    'type' => strlen($document) > 11 ? 'CNPJ' : 'CPF',
                    'number' => $document,
                ],
            ],
        ];

        try {

            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'X-Idempotency-Key' => (string) Str::uuid(),
                ])
                ->post(self::API_URL, $payload);

            if (! $response->successful()) {

                Log::warning('Mercado Pago - resposta não OK ao criar boleto', [
                    'order_id' => $order->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \RuntimeException('Não foi possível gerar o boleto no Mercado Pago.');
            }

            $data = $response->json();

            Log::info('Mercado Pago - resposta OK ao criar boleto', ['body' => $data]);

            $boletoUrl = $data['transaction_details']['external_resource_url']
                ?? $data['transaction_details']['verification_code']
                ?? null;

            $barcode = $data['barcode']['content']
                ?? $data['transaction_details']['digitable_line']
                ?? null;

            return [
                'provider' => 'mercadopago_boleto',
                'txid' => (string) ($data['id'] ?? ''),
                'boleto_url' => $boletoUrl,
                'copy_paste' => $barcode ?? '', // reaproveita o campo pra exibir a linha digitável
                'pix_key' => null,
                'holder_name' => $order->store->name ?? '',
                'amount' => $order->total,
            ];

        } catch (\RuntimeException $e) {

            throw $e;

        } catch (\Throwable $e) {

            Log::warning('Erro ao gerar boleto Mercado Pago', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Erro ao se comunicar com o Mercado Pago.');
        }
    }
}
