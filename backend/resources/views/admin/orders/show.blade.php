@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Pedido #{{ $order->id }}</h2>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
        Voltar
    </a>

</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">

<div class="col-md-8">

    <div class="card mb-4">
        <div class="card-body">

            <h5 class="card-title mb-3">Itens</h5>

            <table class="table">
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td class="text-center">{{ $item->qty }}x</td>
                        <td class="text-end">R$ {{ number_format($item->subtotal,2,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-between">
                <strong>Total</strong>
                <strong class="text-primary">R$ {{ number_format($order->total,2,',','.') }}</strong>
            </div>

        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <h5 class="card-title">Endereço de entrega</h5>

            <p class="mb-0">
                {{ $order->address_snapshot['recipient_name'] ?? '' }}<br>
                {{ $order->address_snapshot['street'] ?? '' }}, {{ $order->address_snapshot['number'] ?? '' }}
                @if(!empty($order->address_snapshot['complement'])) - {{ $order->address_snapshot['complement'] }} @endif
                <br>
                {{ $order->address_snapshot['neighborhood'] ?? '' }},
                {{ $order->address_snapshot['city'] ?? '' }}/{{ $order->address_snapshot['state'] ?? '' }}
                — CEP {{ $order->address_snapshot['zipcode'] ?? '' }}
            </p>

        </div>
    </div>

</div>

<div class="col-md-4">

    <div class="card">
        <div class="card-body">

            <h5 class="card-title">Cliente</h5>

            <p class="mb-1">{{ $order->customer->name }}</p>
            <p class="mb-1">{{ $order->customer->email }}</p>
            <p class="mb-3">{{ $order->customer->phone ?: '—' }}</p>

            <h6>Status</h6>

            <form method="POST" action="{{ route('orders.status', $order) }}">

                @csrf
                @method('PATCH')

                <select name="status" class="form-select mb-2">
                    <option value="pending" @selected($order->status=='pending')>Aguardando pagamento</option>
                    <option value="paid" @selected($order->status=='paid')>Pago</option>
                    <option value="cancelled" @selected($order->status=='cancelled')>Cancelado</option>
                </select>

                <button class="btn btn-primary btn-sm w-100">
                    Atualizar status
                </button>

            </form>

        </div>
    </div>

</div>

</div>

@endsection
