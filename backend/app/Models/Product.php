<?php

namespace App\Models;

use App\Models\Scopes\StoreScope;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [

        'store_id',
        'category_id',

        'media_id',

        'name',
        'slug',
        'description',

        'image',

        'price',
        'sale_price',

        'promotion_start',
        'promotion_end',

        'stock',
        'active',

    ];

    protected $casts = [

        'promotion_start' => 'datetime',
        'promotion_end'   => 'datetime',

    ];

    protected static function booted()
    {
        static::addGlobalScope(new StoreScope);
    }

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function media()
    {
        return $this->belongsTo(
            Media::class,
            'media_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor da imagem
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute()
    {
        if (
            $this->relationLoaded('media')
                ? $this->media
                : $this->media()->exists()
        ) {

            $media = $this->relationLoaded('media')
                ? $this->media
                : $this->media;

            if ($media && $media->file) {

                return asset(
                    'storage/' . $media->file
                );

            }

        }

        if ($this->image) {

            return asset(
                'storage/' . $this->image
            );

        }

        return asset(
            'images/no-image.png'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Promoções
    |--------------------------------------------------------------------------
    */

    public function getCurrentPriceAttribute()
    {
        if (!$this->sale_price) {
            return $this->price;
        }

        $now = now();

        if (
            $this->promotion_start &&
            $now->lt($this->promotion_start)
        ) {
            return $this->price;
        }

        if (
            $this->promotion_end &&
            $now->gt($this->promotion_end)
        ) {
            return $this->price;
        }

        return $this->sale_price;
    }

    public function getIsOnSaleAttribute()
    {
        return $this->current_price < $this->price;
    }

    public function getDiscountPercentAttribute()
    {
        if (!$this->is_on_sale) {
            return 0;
        }

        return round(
            (
                ($this->price - $this->current_price)
                / $this->price
            ) * 100
        );
    }
}