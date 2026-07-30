<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\StorePaymentMethod;

/**
 * TODO: integração real com a API do PagSeguro (PagBank).
 * Mesmo princípio do MercadoPagoGateway: gerar cobrança PIX via API
 * usando $method->credentials, e configurar um endpoint de webhook
 * próprio (ex: /webhooks/pagseguro) para confirmação automática.
 */
class PagSeguroGateway implements PaymentGatewayInterface
{
    public function charge(Order $order, StorePaymentMethod $method): array
    {
        throw new \RuntimeException(
            'Integração com PagSeguro ainda não implementada. '.
            'Configure as credenciais de sandbox para prosseguir.'
        );
    }
}
