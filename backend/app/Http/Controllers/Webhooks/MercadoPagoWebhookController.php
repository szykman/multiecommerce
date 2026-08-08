<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StorePaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Recebe a notificação do Mercado Pago quando o status de um
 * pagamento muda. Por segurança, NUNCA confiamos no corpo da
 * notificação em si — sempre consultamos a API do Mercado Pago
 * de volta, usando o ID recebido, pra confirmar o status real
 * antes de marcar qualquer coisa como paga.
 */
class MercadoPagoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $paymentId = $request->input('data.id')
            ?? $request->query('data.id')
            ?? $request->query('id');

        if (! $paymentId) {
            return response()->json(['ignored' => true]);
        }

        // O provider gravado no Payment é sempre um dos 3 sub-métodos
        // (mercadopago_pix, mercadopago_boleto, mercadopago_checkout_pro)
        // — nunca o literal "mercadopago". Comparar com o literal fazia
        // esse where nunca casar com nada, e todo webhook caía direto
        // no "ignored" antes de consultar a API.
        $payment = Payment::whereIn('provider', [
                'mercadopago_pix',
                'mercadopago_boleto',
                'mercadopago_checkout_pro',
            ])
            ->where('reference', $paymentId)
            ->first();

        if (! $payment) {
            return response()->json(['ignored' => true]);
        }

        $order = $payment->order;

        if (! $order) {
            return response()->json(['ignored' => true]);
        }

        $method = StorePaymentMethod::where('store_id', $order->store_id)
            ->where('provider', $payment->provider)
            ->first();

        $accessToken = $method->credentials['access_token'] ?? null;

        if (! $accessToken) {
            return response()->json(['ignored' => true]);
        }

        try {

            $response = Http::withToken($accessToken)
                ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

            if (! $response->successful()) {

                Log::warning('Mercado Pago webhook - falha ao consultar pagamento', [
                    'payment_id' => $paymentId,
                    'status' => $response->status(),
                ]);

                return response()->json(['ignored' => true]);
            }

            $data = $response->json();

            $status = $data['status'] ?? null; // approved | pending | rejected | cancelled

            if ($status === 'approved' && $payment->status !== 'confirmed') {

                $payment->update([
                    'status' => 'confirmed',
                    'confirmed_by' => 'webhook',
                    'confirmed_at' => now(),
                ]);

                $order->update(['status' => 'paid']);
            }

            if (in_array($status, ['rejected', 'cancelled'], true) && $payment->status !== 'confirmed') {
                $payment->update(['status' => 'cancelled']);
            }

        } catch (\Throwable $e) {

            Log::warning('Erro ao processar webhook Mercado Pago', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(['received' => true]);
    }
}
