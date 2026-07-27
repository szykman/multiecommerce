@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>{{ $customer->name }}</h2>

    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
        Voltar
    </a>

</div>

<div class="row">

<div class="col-md-4 mb-4">

    <div class="card">
        <div class="card-body">

            <h5 class="card-title">Dados</h5>

            <p class="mb-1"><strong>E-mail:</strong> {{ $customer->email }}</p>
            <p class="mb-1"><strong>Telefone:</strong> {{ $customer->phone ?: '—' }}</p>
            <p class="mb-1"><strong>Documento:</strong> {{ $customer->document ?: '—' }}</p>
            <p class="mb-0"><strong>Cliente desde:</strong> {{ $customer->created_at->format('d/m/Y') }}</p>

        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">

            <h5 class="card-title">Endereços</h5>

            @forelse($customer->addresses as $addr)
                <p class="small mb-2 border-bottom pb-2">
                    {{ $addr->street }}, {{ $addr->number }} —
                    {{ $addr->neighborhood }}, {{ $addr->city }}/{{ $addr->state }}
                </p>
            @empty
                <p class="text-muted small mb-0">Nenhum endereço cadastrado.</p>
            @endforelse

        </div>
    </div>

</div>

<div class="col-md-8">

    <div class="card">
        <div class="card-body">

            <h5 class="card-title mb-3">Pedidos</h5>

            @if($customer->orders->count())

            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th class="text-end">Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customer->orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                        <td>{{ $order->status_label }}</td>
                        <td class="text-end">R$ {{ number_format($order->total,2,',','.') }}</td>
                        <td>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary btn-sm">
                                Ver
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @else

            <p class="text-muted mb-0">Nenhum pedido ainda.</p>

            @endif

        </div>
    </div>

</div>

</div>

@endsection
