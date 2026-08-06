@extends('store.layout')

@section('content')

@include('store.partials.header')

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="text-center mb-4">
    <i class="bi bi-check-circle text-success" style="font-size:4rem;"></i>
    <h2 class="mt-3">Pedido Recebido!</h2>
    <p class="text-muted">Pedido #{{ $order->id }} — {{ $order->status_label }}</p>
</div>

<div class="card">
    <div class="card-body">

        <h5 class="card-title">Itens</h5>

        <table class="table">
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td class="text-center">{{ $item->qty }}x</td>
                    <td class="text-end">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-between mt-3">
            <strong>Total</strong>
            <strong class="fs-5 text-primary">R$ {{ number_format($order->total, 2, ',', '.') }}</strong>
        </div>

        <hr>

        <h6>Entregar em</h6>
        <p class="text-muted mb-0">
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

@if($order->status === 'pending')
<div class="alert alert-warning mt-4">
    <i class="bi bi-hourglass-split"></i>
    Falta escolher/concluir a forma de pagamento.
    <a href="{{ route('store.checkout.payment', $order) }}">Ir para pagamento</a>
</div>
@elseif($order->status === 'awaiting_confirmation')
<div class="alert alert-info mt-4">
    <i class="bi bi-info-circle"></i>
    Recebemos seu aviso de pagamento. A loja vai conferir e confirmar em breve.
</div>
@elseif($order->status === 'paid')
<div class="alert alert-success mt-4">
    <i class="bi bi-check-circle"></i>
    Pagamento confirmado! Seu pedido está sendo preparado.
</div>
@endif

<div class="text-center">
    <a href="{{ url('/') }}" class="btn btn-outline-primary">
        Continuar comprando
    </a>
</div>

</div>

</div>

</div>

@endsection
