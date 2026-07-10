@extends('store.layout')

@section('content')

<div class="container py-5">

<h2 class="mb-4">

{{ $category->name }}

</h2>
<div class="row mb-4">

    <div class="col-md-6">


<form
    method="GET"
    action="{{ route('store.category', $category->slug) }}">

    <div class="input-group">

        <input
            type="search"
            name="search"
            class="form-control"
            value="{{ request('search') }}"
            placeholder="Pesquisar nesta categoria...">

        <button
            class="btn btn-primary"
            type="submit">

            Pesquisar

        </button>

    </div>

</form>

    </div>

    <div class="col-md-3">

<select
class="form-select"
onchange="window.location='{{ route('store.category',$category->slug) }}?sort='+this.value">

<option value="">Ordenar</option>

<option value="name&direction=asc">

Nome A-Z

</option>

<option value="name&direction=desc">

Nome Z-A

</option>

<option value="price&direction=asc">

Menor preço

</option>

<option value="price&direction=desc">

Maior preço

</option>

</select>



    </div>

    <div class="col-md-3 text-end">

        <strong>

            {{ $products->total() }}

        </strong>

        produtos

    </div>

</div>

@if($category->description)

<p class="text-muted">

{{ $category->description }}

</p>

@endif

<div class="row">

@forelse($products as $product)

<div class="col-md-3 mb-4">

<div class="card h-100 shadow-sm">

@if($product->image)

<img
src="{{ asset('storage/'.$product->image) }}"
class="card-img-top"
style="height:220px;object-fit:cover;">

@endif

<div class="card-body">

<h5>

{{ $product->name }}

</h5>

<p class="fw-bold text-primary">

R$ {{ number_format($product->price,2,',','.') }}

</p>

<a
href="/produto/{{ $product->slug }}"
class="btn btn-primary w-100">

Ver Produto

</a>

</div>

</div>

</div>

@empty

<div class="col">

Nenhum produto encontrado.

</div>

@endforelse

</div>

<div class="mt-4">

{{ $products->links() }}

</div>


</div>

@endsection
