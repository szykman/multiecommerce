@extends('admin.layouts.app')

@section('content')


<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Produtos</h2>


<form method="GET" class="row g-2 mb-4">

    <div class="col-md-5">

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            class="form-control"
            placeholder="Buscar produto">

    </div>

    <div class="col-md-4">

        <select
            name="category_id"
            class="form-select">

            <option value="">

                Todas Categorias

            </option>

            @foreach($categories as $category)

                <option
                    value="{{ $category->id }}"
                    @selected(request('category_id')==$category->id)>

                    {{ $category->name }}

                </option>

            @endforeach

        </select>

    </div>

    <div class="col-md-3">

        <button class="btn btn-dark w-100">

            Buscar

        </button>

</div>

</form>

  <a href="{{ route('products.create') }}" class="btn btn-primary">
        + Novo Produto
    </a>

</div>

<table class="table table-striped table-hover align-middle">

    <thead class="table-dark">

        <tr>
            <th>Imagem</th>

<th>
    <a class="text-white text-decoration-none"
       href="?sort=name&direction={{ request('direction')=='asc' ? 'desc':'asc' }}">

        Produto

        @if(request('sort')=='name')
            {{ request('direction')=='asc' ? '▲' : '▼' }}
        @endif

    </a>
</th>


<th>
    <a class="text-white text-decoration-none"
       href="?sort=price&direction={{ request('direction')=='asc' ? 'desc':'asc' }}">

        Preço

        @if(request('sort')=='price')
            {{ request('direction')=='asc' ? '▲' : '▼' }}
        @endif

    </a>
</th>


<th>
    <a class="text-white text-decoration-none"
       href="?sort=stock&direction={{ request('direction')=='asc' ? 'desc':'asc' }}">

        Estoque

        @if(request('sort')=='stock')
            {{ request('direction')=='asc' ? '▲' : '▼' }}
        @endif

    </a>
</th>
            <th>


    <a class="text-white text-decoration-none"
       href="?sort=stock&direction={{ request('direction')=='asc' ? 'desc':'asc' }}">

        Status

        @if(request('sort')=='stock')
            {{ request('direction')=='asc' ? '▲' : '▼' }}
        @endif

    </a>




</th>
            <th width="220">Ações</th>
        </tr>

    </thead>

    <tbody>

    @forelse($products as $product)

        <tr>

            <td>

                @if($product->image)

                    <img
                        src="{{ asset('storage/'.$product->image) }}"
                        width="70"
                        height="70"
                        style="object-fit:cover;border-radius:8px;">

                @else

                    <span class="text-muted">
                        —
                    </span>

                @endif

            </td>

            <td>

                <strong>{{ $product->name }}</strong>

                <br>

                <small class="text-muted">
                    {{ $product->slug }}
                </small>

                <br>


    @if($product->category)

        <span class="badge bg-info text-dark">
            {{ $product->category->name }}
        </span>

    @else

        <span class="text-muted">
            Sem categoria
        </span>

    @endif

</td>

            <td>

                R$ {{ number_format($product->price,2,',','.') }}

            </td>

            <td>

                {{ $product->stock }}

            </td>

<td>

<form
    action="{{ route('products.toggle', $product) }}"
    method="POST">

    @csrf
    @method('PATCH')

    <button
        class="btn btn-sm {{ $product->active ? 'btn-success' : 'btn-secondary' }}">

        {{ $product->active ? 'Ativo' : 'Inativo' }}

    </button>

</form>

</td>



             <td>

                                <a
                                    href="{{ route('products.edit',$product) }}"
                                    class="btn btn-sm btn-outline-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>
<form
action="{{ route('products.destroy',$product) }}"
method="POST"
class="d-inline"
onsubmit="return confirm('Deseja realmente excluir este produto?')">

@csrf
@method('DELETE')

<button class="btn btn-sm btn-danger">

<i class="bi bi-trash"></i>

</button>

</form>


                            </td>

	


        </tr>

    @empty

        <tr>

            <td colspan="6" class="text-center">

                Nenhum produto cadastrado.

            </td>

        </tr>

    @endforelse

    </tbody>

</table>

<div class="mt-3">

{{ $products->links() }}

</div>

@endsection
