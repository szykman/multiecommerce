@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Pedidos</h2>

</div>

<form method="GET" class="row g-2 mb-4">

    <div class="col-md-3">

        <select name="status" class="form-select">
            <option value="">Todos os status</option>
            <option value="pending" @selected(request('status')=='pending')>Aguardando pagamento</option>
            <option value="paid" @selected(request('status')=='paid')>Pago</option>
            <option value="cancelled" @selected(request('status')=='cancelled')>Cancelado</option>
        </select>

    </div>

    <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100">Filtrar</button>
    </div>

</form>

@if($orders->count())

<div class="table-responsive">

    <table class="table align-middle">

        <thead>
            <tr>
                <th>Pedido</th>
                <th>Cliente</th>
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
                <td>{{ $order->customer->name ?? '—' }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="badge bg-{{ $order->status === 'paid' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                        {{ $order->status_label }}
                    </span>
                </td>
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

</div>

<div class="mt-3">
    {{ $orders->links() }}
</div>

@else

<div class="alert alert-info">
    Nenhum pedido ainda.
</div>

@endif

@endsection
