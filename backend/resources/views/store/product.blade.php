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
class="img-fluid rounded shadow"
src="{{ asset('storage/'.$product->image) }}">

@endif

</div>

<div class="col-lg-6">

<h1>

{{ $product->name }}

</h1>


<div class="mb-3">

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

<div class="mb-4">

<label class="form-label">

Quantidade

</label>

<input
type="number"
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
method="POST"
action="{{ route('store.cart.add',$product->slug) }}">

@csrf

<button
class="btn btn-outline-secondary w-100">

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

@endsection
