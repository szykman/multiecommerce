<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\StorePaymentMethod;

/**
 * TODO: integração real com a API do Mercado Pago.
 *
 * Quando tiver as credenciais de sandbox, o fluxo esperado é:
 * 1. composer require mercadopago/dx-php
 * 2. Usar $method->credentials['access_token'] para autenticar
 * 3. Criar um Payment (PIX dinâmico) via API, que devolve
 *    QR code + copia-e-cola + um payment_id do Mercado Pago
 * 4. Configurar um endpoint de webhook (rota pública, ex:
 *    /webhooks/mercadopago) para receber a notificação de
 *    pagamento aprovado e então marcar Payment/Order como pagos
 *    automaticamente — sem depender do cliente clicar "já paguei".
 */
class MercadoPagoGateway implements PaymentGatewayInterface
{
    public function charge(Order $order, StorePaymentMethod $method): array
    {
        throw new \RuntimeException(
            'Integração com Mercado Pago ainda não implementada. '.
            'Configure as credenciais de sandbox para prosseguir.'
        );
    }
}
