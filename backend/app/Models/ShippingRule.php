<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRule extends Model
{
    protected $fillable = [
        'store_id',
        'name',
        'type',
        'states',
        'min_weight',
        'max_weight',
        'price',
        'estimated_days',
        'active',
        'position',
    ];

    protected $casts = [
        'states' => 'array',
        'min_weight' => 'decimal:3',
        'max_weight' => 'decimal:3',
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Verifica se esta regra se aplica a um destino (UF) e peso
     * total de carrinho específicos.
     */
    public function matches(string $stateUf, float $totalWeight): bool
    {
        if (! $this->active) {
            return false;
        }

        if ($this->type === 'region' && ! empty($this->states)) {
            if (! in_array(strtoupper($stateUf), $this->states)) {
                return false;
            }
        }

        if ($totalWeight < (float) $this->min_weight) {
            return false;
        }

        if ($this->max_weight !== null && $totalWeight > (float) $this->max_weight) {
            return false;
        }

        return true;
    }
}
