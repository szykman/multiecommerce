<?php

namespace App\Services\Shipping;

use App\Models\Store;
use Illuminate\Support\Collection;

class ShippingQuoteService
{
    public function quote(Store $store, string $destinationZipcode, string $destinationState, Collection $cartItems): array
    {
        $options = [];

        $calculators = [
            new FixedRegionShippingCalculator(),
            new CorreiosShippingCalculator(),
        ];

        foreach ($calculators as $calculator) {

            $result = $calculator->calculate($store, $destinationZipcode, $destinationState, $cartItems);

            $options = array_merge($options, $result);
        }

        // Ordena do mais barato para o mais caro
        usort($options, fn ($a, $b) => $a['price'] <=> $b['price']);

        return $options;
    }
}
