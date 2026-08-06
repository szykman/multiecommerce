<?php

namespace App\Services\Shipping;

use App\Models\Store;
use Illuminate\Support\Collection;

interface ShippingCalculatorInterface
{
    /**
     * Calcula as opções de frete disponíveis.
     *
     * @param  Collection  $cartItems  itens do carrinho (array com id, qty, etc.)
     * @return array<int, array{name: string, price: float, estimated_days: ?int, provider: string}>
     */
    public function calculate(Store $store, string $destinationZipcode, string $destinationState, Collection $cartItems): array;
}
