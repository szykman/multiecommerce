<?php

namespace App\Services\Payments;

use InvalidArgumentException;

class PaymentGatewayFactory
{
    public static function make(string $provider): PaymentGatewayInterface
    {
        return match ($provider) {
            'pix_manual' => new PixManualGateway(),
            'mercadopago_pix' => new MercadoPagoPixGateway(),
            'mercadopago_boleto' => new MercadoPagoBoletoGateway(),
            'mercadopago_checkout_pro' => new MercadoPagoCheckoutProGateway(),
            'mercadopago' => new MercadoPagoGateway(),
            'pagseguro' => new PagSeguroGateway(),
            default => throw new InvalidArgumentException("Gateway desconhecido: {$provider}"),
        };
    }
}
