<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'sale_price',
        'stock',
        'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Valores de opção que compõem esta variante
     * (ex: "Preto" + "M").
     */
    public function optionValues()
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            'product_variant_values'
        );
    }

    /**
     * Sincroniza o estoque do produto pai sempre que uma variante
     * é criada, atualizada ou removida — o campo `stock` de
     * `products` passa a ser a soma das variantes ativas quando o
     * produto tem variação (has_variants = true).
     */
    protected static function booted()
    {
        static::saved(function (ProductVariant $variant) {
            $variant->syncProductStock();
        });

        static::deleted(function (ProductVariant $variant) {
            $variant->syncProductStock();
        });
    }

    public function syncProductStock(): void
    {
        $product = $this->product;

        if (! $product || ! $product->has_variants) {
            return;
        }

        $total = static::where('product_id', $product->id)
            ->where('active', true)
            ->sum('stock');

        // updateQuietly evita re-disparar eventos do Product
        // desnecessariamente; se o Product model não tiver esse
        // método (Laravel < 11), trocar por:
        // $product->stock = $total; $product->saveQuietly();
        $product->stock = $total;
        $product->saveQuietly();
    }
}
