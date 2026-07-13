<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\StoreScope;

class Category extends Model
{
    protected $fillable = [
        'store_id',
	'type',
        'name',
        'slug',
        'active'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new StoreScope);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
