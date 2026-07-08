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
}
