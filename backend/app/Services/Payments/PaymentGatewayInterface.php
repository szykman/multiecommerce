<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\StorePaymentMethod;

interface PaymentGatewayInterface
{
    /**
     * Gera a cobrança para o pedido e devolve os dados necessários
     * para exibir ao cliente (QR code, copia-e-cola, link de
     * redirecionamento, etc. — cada gateway devolve um formato
     * próprio dentro do array).
     */
    public function charge(Order $order, StorePaymentMethod $method): array;
}
