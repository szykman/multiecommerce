@extends('store.layout')

@section('content')

@include('store.partials.header')

<div class="container py-5">

<h2 class="mb-4">
    <i class="bi bi-bag"></i>
    Meus Pedidos
</h2>

@if($orders->count())

<div class="table-responsive">
    <table class="table align-middle">
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
            @foreach($orders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="badge bg-{{ $order->status === 'paid' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                        {{ $order->status_label }}
                    </span>
                </td>
                <td class="text-end">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                <td>
                    <a href="{{ route('store.checkout.confirmation', $order) }}" class="btn btn-outline-secondary btn-sm">
                        Ver detalhes
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@else

<div class="alert alert-info">
    Você ainda não tem pedidos.
</div>

@endif

</div>

@endsection
