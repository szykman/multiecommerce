@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Clientes</h2>

</div>

<form method="GET" class="row g-2 mb-4">

    <div class="col-md-5">

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            class="form-control"
            placeholder="Buscar por nome ou e-mail">

    </div>

    <div class="col-md-2">

        <button class="btn btn-outline-secondary w-100">
            Filtrar
        </button>

    </div>

</form>

@if($customers->count())

<div class="table-responsive">

    <table class="table align-middle">

        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th class="text-center">Pedidos</th>
                <th>Cadastrado em</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            @foreach($customers as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->phone ?: '—' }}</td>
                <td class="text-center">
                    <span class="badge bg-secondary">{{ $customer->orders_count }}</span>
                </td>
                <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary btn-sm">
                        Ver
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>

</div>

<div class="mt-3">
    {{ $customers->links() }}
</div>

@else

<div class="alert alert-info">
    Nenhum cliente cadastrado ainda.
</div>

@endif

@endsection
