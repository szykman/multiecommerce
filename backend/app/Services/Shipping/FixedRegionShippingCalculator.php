<?php

namespace App\Services\Shipping;

use App\Models\Product;
use App\Models\ShippingRule;
use App\Models\Store;
use Illuminate\Support\Collection;

class FixedRegionShippingCalculator implements ShippingCalculatorInterface
{
    public function calculate(Store $store, string $destinationZipcode, string $destinationState, Collection $cartItems): array
    {
        $totalWeight = $this->calculateTotalWeight($cartItems);

        $rules = ShippingRule::where('store_id', $store->id)
            ->where('active', true)
            ->orderBy('position')
            ->get();

        $options = [];

        foreach ($rules as $rule) {

            if (! $rule->matches($destinationState, $totalWeight)) {
                continue;
            }

            $options[] = [
                'provider' => 'fixed_region',
                'name' => $rule->name,
                'price' => (float) $rule->price,
                'estimated_days' => $rule->estimated_days,
            ];
        }

        return $options;
    }

    protected function calculateTotalWeight(Collection $cartItems): float
    {
        $total = 0.0;

        foreach ($cartItems as $item) {

            $product = Product::find($item['id']);

            $weight = $product?->weight ?? 0;

            $total += (float) $weight * (int) $item['qty'];
        }

        return $total;
    }
}
