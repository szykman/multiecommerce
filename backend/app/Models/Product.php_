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
        'has_variants',

    ];

    protected $casts = [

        'promotion_start' => 'datetime',
        'promotion_end'   => 'datetime',
        'has_variants'    => 'boolean',

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

public function featuredImage()
{
    return $this->media;
}

public function gallery()
{
    return $this->hasMany(ProductMedia::class)
        ->orderBy('position');
}

    /*
    |--------------------------------------------------------------------------
    | Variações
    |--------------------------------------------------------------------------
    */

    public function options()
    {
        return $this->hasMany(ProductOption::class)
            ->orderBy('position');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants()
    {
        return $this->variants()->where('active', true);
    }

    /**
     * Preço "a partir de" quando o produto tem variantes — o menor
     * preço entre as variantes ativas. Cai no preço normal do
     * produto se não houver variantes.
     */
    public function getMinVariantPriceAttribute()
    {
        if (! $this->has_variants) {
            return $this->price;
        }

        $min = $this->activeVariants()
            ->orderBy('price')
            ->value('price');

        return $min ?? $this->price;
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

public function averageRating()
{
    return $this->reviews()
        ->where('approved', true)
        ->avg('rating');
}

    /*
    |--------------------------------------------------------------------------
    | Accessor da imagem
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute()
    {
        return $this->resolveImageUrl('file');
    }

    /**
     * Versão pequena (300px, WEBP) — usada em grades/listagens
     * (admin: biblioteca, galeria; storefront: cards de produto).
     */
    public function getImageThumbnailUrlAttribute()
    {
        return $this->resolveImageUrl('thumbnail');
    }

    /**
     * Versão média (1200px, WEBP) — usada em exibições maiores
     * (imagem principal do produto, banners de página CMS).
     */
    public function getImagePreviewUrlAttribute()
    {
        return $this->resolveImageUrl('preview');
    }

    /**
     * Resolve a URL da imagem do produto/página para um dado tamanho.
     *
     * Prioridade: mídia associada (media_id) no tamanho pedido
     * -> mídia associada no arquivo original (fallback se o tamanho
     * pedido ainda não foi gerado) -> coluna "image" legada
     * -> placeholder.
     */
    protected function resolveImageUrl(string $size): string
    {
        $media = $this->media;

        if ($media) {

            $value = $size !== 'file'
                ? ($media->{$size} ?: $media->file)
                : $media->file;

            if ($value) {
                return asset('storage/'.$value);
            }
        }

        if ($this->image) {
            return asset('storage/'.$this->image);
        }

        return asset('images/no-image.png');
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

    /*
    |--------------------------------------------------------------------------
    | Avaliações
    |--------------------------------------------------------------------------
    */

    public function getAverageRatingAttribute()
    {
        return round(

            $this->reviews()
                ->where('approved', true)
                ->avg('rating') ?? 0,

            1

        );
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()
            ->where('approved', true)
            ->count();
    }

    public function getRatingStarsAttribute()
    {
        return round($this->average_rating);
    }
}
