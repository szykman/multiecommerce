@extends('store.layout')

@section('content')

@include('store.partials.header')

<!-- HERO -->

<!-- HERO BANNER -->

<section class="position-relative">

@if($settings?->banner)

<img
src="{{ asset('storage/'.$settings->banner) }}"
class="w-100"
style="height:600px;object-fit:cover;">

@else

<img
src="https://placehold.co/1600x600?text=Banner"
class="w-100"
style="height:600px;object-fit:cover;">

@endif

<div class="position-absolute top-50 start-50 translate-middle text-center text-white">

<h1 class="display-3 fw-bold">

{{ $store->name }}

</h1>


<p class="fs-4">

Os melhores produtos em um só lugar.

</p>


<a href="#produtos"
class="btn btn-primary btn-lg px-5">

Comprar Agora

</a>


</div>


</section>

<!-- SEARCH BOX -->

<section class="py-4 bg-white">

<div class="container">

<form method="GET" action="/">

<div class="input-group input-group-lg shadow-sm">

<input
type="search"
name="search"
class="form-control"
placeholder="O que você está procurando?"
value="{{ request('search') }}">


<button class="btn btn-primary px-4">

<i class="bi bi-search"></i>

Buscar

</button>

</div>

</form>

</div>

</section>


<!-- CATEGORIAS -->

<section

id="categorias"

class="py-5 bg-white">

<div class="container">

<div class="row">

@forelse($categories as $category)

<div class="col-md-3 mb-3">

<div class="card product-card shadow-sm h-100">

<a
href="{{ route('store.category', $category->slug) }}"
class="text-decoration-none text-dark">

<div class="card product-card shadow-sm h-100">

    <div class="card-body text-center">

        <h5>{{ $category->name }}</h5>

        <p class="text-muted">
            {{ $category->products_count ?? '' }}
        </p>

    </div>

</div>

</a>

</div>

</div>

@empty

<div class="col">

Nenhuma categoria cadastrada.

</div>

@endforelse

</div>

</div>

</section>





<!-- PRODUTOS -->

<section

id="produtos"

class="py-5">

<div class="container">

<div class="row">

@forelse($products as $product)

<div class="col-lg-3 col-md-4 col-sm-6 mb-4">

<div class="card h-100 shadow-sm">

@if($product->image)

<img

src="{{ asset('storage/'.$product->image) }}"

class="card-img-top"

style="height:220px;object-fit:cover;">

@else

<img

src="https://placehold.co/400x300?text=Produto"

class="card-img-top">

@endif

<div class="card-body">

<h5>

{{ $product->name }}

</h5>

<div class="mb-2">

    @for($i=1;$i<=5;$i++)

        @if($i <= $product->rating_stars)

            <i class="bi bi-star-fill text-warning"></i>

        @else

            <i class="bi bi-star text-warning"></i>

        @endif

    @endfor

    <small class="text-muted ms-2">

        {{ number_format($product->average_rating,1) }}

        ({{ $product->reviews_count }})

    </small>

</div>

<button
    class="favorite-btn {{ in_array($product->id, session('favorites',[])) ? 'active' : '' }}"
    type="button"
    data-product="{{ $product->id }}">

    <i class="bi {{ in_array($product->id, session('favorites',[])) ? 'bi-heart-fill' : 'bi-heart' }}"></i>

</button>

<p class="text-muted small">

{{ Str::limit($product->description,80) }}

</p>

<h4 class="text-success">

@if($product->is_on_sale)

<div>

<small
class="text-muted text-decoration-line-through">

R$
{{ number_format($product->price,2,',','.') }}

</small>

</div>

<div
class="fw-bold text-danger fs-4">

R$
{{ number_format($product->current_price,2,',','.') }}

</div>

<span class="badge bg-danger">

-{{ $product->discount_percent }}%

</span>

@else

<div
class="fw-bold fs-4">

R$
{{ number_format($product->price,2,',','.') }}

</div>

@endif

</h4>

<a

href="{{ route('store.product',$product->slug) }}"


class="btn btn-primary w-100">

Ver Produto

</a>

</div>

</div>

</div>

@empty

<div class="col">

Nenhum produto cadastrado.

</div>

@endforelse

</div>

</div>

</section>





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



@endsection
