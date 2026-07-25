@extends('store.layout')

@section('content')

@include('store.partials.header')

<div class="container py-5">

<nav class="mb-4">

<a
href="{{ url('/') }}">

Home

</a>

@if($product->category)

/

<a
href="{{ route('store.category',$product->category->slug) }}">

{{ $product->category->name }}

</a>

@endif

/

<strong>

{{ $product->name }}

</strong>

</nav>

<div class="row">

<div class="col-lg-6">



@if($product->image)

<img
id="mainProductImage"
class="img-fluid rounded shadow w-100"
style="
max-height:600px;
object-fit:contain;
cursor:zoom-in;
"
src="{{ $product->image_preview_url }}"
data-zoom="{{ $product->image_url }}">

@endif




@if($product->gallery->count())

<hr class="my-4">

<h5>Galeria</h5>


<div class="d-flex flex-wrap gap-2">

<img
src="{{ $product->image_thumbnail_url }}"
data-image="{{ $product->image_preview_url }}"
data-zoom="{{ $product->image_url }}"
class="gallery-thumb img-thumbnail border-primary border-3"
style="
width:90px;
height:90px;
object-fit:cover;
cursor:pointer;
transition:.2s;
">


@foreach($product->gallery as $photo)

@if($photo->media->type == 'image')

<img
src="{{ asset('storage/'.($photo->media->thumbnail ?: $photo->media->file)) }}"
data-image="{{ asset('storage/'.($photo->media->preview ?: $photo->media->file)) }}"
data-zoom="{{ asset('storage/'.$photo->media->file) }}"
class="gallery-thumb img-thumbnail"
style="
width:90px;
height:90px;
object-fit:cover;
cursor:pointer;
transition:.2s;
">

@endif

@endforeach

</div>



@else

<div class="alert alert-danger mt-3">

A galeria está vazia.

</div>

@endif


</div>

<div class="col-lg-6">

<h1>

{{ $product->name }}

</h1>

<div class="mb-3">

    @for($i=1;$i<=5;$i++)

        @if($i <= $product->rating_stars)

            <i class="bi bi-star-fill text-warning fs-5"></i>

        @else

            <i class="bi bi-star text-warning fs-5"></i>

        @endif

    @endfor

    <span class="ms-2">

        <strong>

            {{ number_format($product->average_rating,1) }}

        </strong>

        <small class="text-muted">

            ({{ $product->reviews_count }} avaliações)

        </small>

    </span>

</div>

<button
class="favorite-btn {{ in_array($product->id, session('favorites',[])) ? 'active' : '' }}"
type="button"
data-product="{{ $product->id }}">

<i class="bi {{ in_array($product->id, session('favorites',[])) ? 'bi-heart-fill' : 'bi-heart' }}"></i>

</button>


<div class="mb-3" id="stock_badge">

@if($product->stock > 0)

<span class="badge bg-success">

<i class="bi bi-check-circle"></i>

Em estoque

</span>

@else

<span class="badge bg-danger">

Produto indisponível

</span>

@endif

</div>

<div id="price_display">

<h2 class="text-primary mb-4">

@if($product->is_on_sale)

<h5>

<span
class="text-decoration-line-through text-muted">

R$
{{ number_format($product->price,2,',','.') }}

</span>

</h5>

<h2 class="text-danger">

R$
{{ number_format($product->current_price,2,',','.') }}

</h2>

<span class="badge bg-danger">

Economize
{{ $product->discount_percent }}%

</span>

@else

<h2>

R$
{{ number_format($product->price,2,',','.') }}

</h2>

@endif

</h2>

</div>

@if($product->has_variants && $product->options->count())

<div id="variants_selector" class="mb-4">

    @foreach($product->options as $option)

    <div class="mb-3">

        <label class="form-label d-block">
            <b>{{ $option->name }}</b>
        </label>

        <div class="d-flex flex-wrap gap-2 option-group" data-option-id="{{ $option->id }}">

            @foreach($option->values as $value)

            <button
                type="button"
                class="btn btn-outline-secondary btn-sm option-value-btn"
                data-option-id="{{ $option->id }}"
                data-value-id="{{ $value->id }}">
                {{ $value->value }}
            </button>

            @endforeach

        </div>

    </div>

    @endforeach

    <div id="variant_feedback" class="text-muted small"></div>

</div>

@endif

<div class="mb-4">

<label class="form-label">

Quantidade

</label>

<input
type="number"
name="quantity"
id="quantity_input"
form="add_to_cart_form"
class="form-control"
value="1"
min="1"
style="max-width:120px;">

</div>



<hr>

<p>

{!! nl2br(e($product->description)) !!}

</p>

<div class="d-grid gap-2">

<button
class="btn btn-primary btn-lg">

<i class="bi bi-bag-check"></i>

Comprar Agora

</button>

<form
id="add_to_cart_form"
method="POST"
action="{{ route('store.cart.add',$product->slug) }}">

@csrf

<input type="hidden" name="variant_id" id="variant_id_input" value="">

<button
id="add_to_cart_btn"
class="btn btn-outline-secondary w-100"
{{ $product->has_variants ? 'disabled' : '' }}>

<i class="bi bi-cart-plus"></i>

Adicionar ao Carrinho

</button>

</form>

</div>

</div>

</div>

</div>


@if($relatedProducts->count())

<hr class="my-5">

<div class="d-flex justify-content-between align-items-center mb-4">

<h3 class="mb-0">

Você também pode gostar

</h3>

</div>

<div class="row">

@foreach($relatedProducts as $related)

<div class="col-md-3 mb-4">

<div class="card product-card h-100">

@if($related->image)

<img
class="card-img-top"
src="{{ asset('storage/'.$related->image) }}">

@endif

<div class="card-body">

<h5>

{{ $related->name }}

</h5>

<p>

R$ {{ number_format($related->price,2,',','.') }}

</p>

<a
href="{{ route('store.product',$related->slug) }}"
class="btn btn-primary w-100">

Ver Produto

</a>

</div>

</div>

</div>

@endforeach

</div>

@endif

@include('store.partials.footer')


<script>

document.querySelectorAll('.favorite-btn').forEach(function(btn){

    btn.addEventListener('click',function(){

        let product = this.dataset.product;

        fetch('/favorites/toggle/'+product,{

            method:'POST',

            headers:{
                'X-CSRF-TOKEN':'{{ csrf_token() }}',
                'Accept':'application/json'
            }

        })

        .then(r=>r.json())

        .then(data=>{

            this.classList.toggle('active',data.favorite);

            let icon=this.querySelector('i');

            if(data.favorite){

                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill');

            }else{

                icon.classList.remove('bi-heart-fill');
                icon.classList.add('bi-heart');

            }

        });

    });

});

</script>


<script>

const mainImage = document.getElementById('mainProductImage');

let zoom = new Drift(mainImage,{
    paneContainer: document.querySelector('.col-lg-6'),
    inlinePane: false,
    hoverBoundingBox: true,
    zoomFactor: 3
});

document.querySelectorAll('.gallery-thumb').forEach(function(img){

    img.addEventListener('click',function(){

        mainImage.src=this.dataset.image;

        mainImage.setAttribute(
            'data-zoom',
            this.dataset.zoom
        );

        zoom.destroy();

        zoom=new Drift(mainImage,{
            paneContainer: document.querySelector('.col-lg-6'),
            inlinePane:false,
            hoverBoundingBox:true,
            zoomFactor:3
        });

        document.querySelectorAll('.gallery-thumb').forEach(function(i){
            i.classList.remove('border-primary','border-3');
        });

        this.classList.add('border-primary','border-3');

    });

});

</script>

@if($product->has_variants && $product->options->count())
@php
    $productOptionsForJs = $product->options->map(function($option){
        return [
            'id' => $option->id,
            'name' => $option->name,
            'values' => $option->values->pluck('id'),
        ];
    });

    $productVariantsForJs = $product->variants->map(function($variant){
        return [
            'id' => $variant->id,
            'price' => (float) $variant->price,
            'sale_price' => $variant->sale_price ? (float) $variant->sale_price : null,
            'stock' => $variant->stock,
            'option_value_ids' => $variant->optionValues->pluck('id'),
        ];
    });
@endphp

<script>

/*
|--------------------------------------------------------------------------
| Seletor de Variações
|--------------------------------------------------------------------------
*/

const productOptions = @json($productOptionsForJs);

const productVariants = @json($productVariantsForJs);

const selected = {};

const variantFeedback = document.getElementById('variant_feedback');
const variantIdInput = document.getElementById('variant_id_input');
const addToCartBtn = document.getElementById('add_to_cart_btn');
const priceDisplay = document.getElementById('price_display');
const stockBadge = document.getElementById('stock_badge');
const quantityInput = document.getElementById('quantity_input');

function formatPrice(value){
    return 'R$ ' + value.toFixed(2).replace('.', ',');
}

function findMatchingVariant(){

    const selectedOptionIds = Object.keys(selected);

    if(selectedOptionIds.length !== productOptions.length){
        return null;
    }

    const selectedValueIds = Object.values(selected).map(Number).sort();

    return productVariants.find(function(variant){

        const variantValueIds = variant.option_value_ids.map(Number).sort();

        if(variantValueIds.length !== selectedValueIds.length){
            return false;
        }

        return variantValueIds.every(function(id, index){
            return id === selectedValueIds[index];
        });

    }) || null;
}

/**
 * Marca como indisponível (visualmente) os valores que, combinados
 * com o que já está selecionado nas outras opções, não levam a
 * nenhuma variante ativa com estoque.
 */
function refreshAvailability(){

    document.querySelectorAll('.option-group').forEach(function(group){

        const optionId = group.dataset.optionId;

        group.querySelectorAll('.option-value-btn').forEach(function(btn){

            const valueId = btn.dataset.valueId;

            const hypothetical = Object.assign({}, selected, {
                [optionId]: valueId,
            });

            const hasStock = productVariants.some(function(variant){

                return Object.keys(hypothetical).every(function(optId){

                    return variant.option_value_ids
                        .map(String)
                        .includes(String(hypothetical[optId]));

                }) && variant.stock > 0;

            });

            btn.classList.toggle('disabled', !hasStock);
            btn.style.opacity = hasStock ? '1' : '0.4';

        });

    });
}

function updateSelectionUI(){

    const variant = findMatchingVariant();

    refreshAvailability();

    if(!variant){

        variantIdInput.value = '';
        addToCartBtn.disabled = true;

        const missing = productOptions.length - Object.keys(selected).length;

        variantFeedback.textContent = missing > 0
            ? 'Selecione ' + missing + ' opção(ões) para continuar.'
            : 'Combinação indisponível.';

        return;
    }

    variantIdInput.value = variant.id;

    const displayPrice = variant.sale_price || variant.price;

    let priceHtml = '';

    if(variant.sale_price){
        priceHtml += '<h5><span class="text-decoration-line-through text-muted">'
            + formatPrice(variant.price) + '</span></h5>';
        priceHtml += '<h2 class="text-danger">' + formatPrice(variant.sale_price) + '</h2>';
    }else{
        priceHtml += '<h2>' + formatPrice(variant.price) + '</h2>';
    }

    priceDisplay.innerHTML = priceHtml;

    if(variant.stock > 0){

        stockBadge.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Em estoque</span>';
        addToCartBtn.disabled = false;
        quantityInput.max = variant.stock;
        variantFeedback.textContent = '';

    }else{

        stockBadge.innerHTML = '<span class="badge bg-danger">Produto indisponível</span>';
        addToCartBtn.disabled = true;
        variantFeedback.textContent = 'Esta combinação está sem estoque.';

    }

}

document.querySelectorAll('.option-value-btn').forEach(function(btn){

    btn.addEventListener('click', function(){

        if(this.classList.contains('disabled')){
            return;
        }

        const optionId = this.dataset.optionId;
        const valueId = this.dataset.valueId;

        // desmarca os irmãos da mesma opção
        document.querySelectorAll(
            '.option-value-btn[data-option-id="' + optionId + '"]'
        ).forEach(function(sibling){
            sibling.classList.remove('btn-primary', 'active');
            sibling.classList.add('btn-outline-secondary');
        });

        this.classList.remove('btn-outline-secondary');
        this.classList.add('btn-primary', 'active');

        selected[optionId] = valueId;

        updateSelectionUI();

    });

});

// Estado inicial: nenhuma opção selecionada, botão desabilitado
updateSelectionUI();

</script>
@endif

@endsection
