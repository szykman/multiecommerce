@extends('store.layout')

@section('content')

@include('store.partials.header')

<!-- HERO -->

<!-- HERO BANNER -->

<section class="position-relative">

<img
src="https://placehold.co/1600x600?text=Banner+da+Loja"
class="w-100"
style="height:600px;object-fit:cover;">


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

<h2 class="mb-4">

Categorias

</h2>

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

<h2 class="mb-4">

Produtos

</h2>

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

<p class="text-muted small">

{{ Str::limit($product->description,80) }}

</p>

<h4 class="text-success">

R$

{{ number_format($product->price,2,',','.') }}

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

@endsection
