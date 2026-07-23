<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $fillable = [

        'store_id',

        'product_id',

        'name',

        'email',

        'rating',

        'title',

        'comment',

        'approved'

    ];

    protected $casts = [

        'approved' => 'boolean',

    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
