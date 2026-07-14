<?php

namespace App\Models;
use App\Models\Scopes\StoreScope;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

protected $fillable = [
    'store_id',
    'category_id',
    'name',
    'slug',
    'description',
    'image',
    'price',
    'stock',
    'active',
'sale_price',
'promotion_start',
'promotion_end',
];


protected $casts = [
    'promotion_start' => 'datetime',
    'promotion_end' => 'datetime',
];

protected static function booted()
{
    static::addGlobalScope(new StoreScope);
}

public function category()
{
    return $this->belongsTo(Category::class);
}

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

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
