<?php

namespace App\Services\Payments;

use InvalidArgumentException;

class PaymentGatewayFactory
{
    public static function make(string $provider): PaymentGatewayInterface
    {
        return match ($provider) {
            'pix_manual' => new PixManualGateway(),
            'mercadopago' => new MercadoPagoGateway(),
            'pagseguro' => new PagSeguroGateway(),
            default => throw new InvalidArgumentException("Gateway desconhecido: {$provider}"),
        };
    }
}
