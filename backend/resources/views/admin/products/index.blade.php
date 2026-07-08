@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Produtos</h2>

    <a href="/admin/products/create" class="btn btn-primary">
        + Novo Produto
    </a>

</div>

<table class="table table-striped table-hover align-middle">

    <thead class="table-dark">

        <tr>
            <th width="90">Imagem</th>
            <th>Produto</th>
            <th>Preço</th>
            <th>Estoque</th>
            <th>Status</th>
            <th width="170">Ações</th>
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

            </td>

            <td>

                R$ {{ number_format($product->price,2,',','.') }}

            </td>

            <td>

                {{ $product->stock }}

            </td>

            <td>

                @if($product->active)

                    <span class="badge bg-success">
                        Ativo
                    </span>

                @else

                    <span class="badge bg-danger">
                        Inativo
                    </span>

                @endif

            </td>

            <td>

                <a href="/admin/products/{{ $product->id }}/edit"
                   class="btn btn-warning btn-sm">

                    Editar

                </a>

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

@endsection
