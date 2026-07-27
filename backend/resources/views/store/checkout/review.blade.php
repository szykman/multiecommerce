@extends('store.layout')

@section('content')

@include('store.partials.header')

<div class="container py-5">

<h2 class="mb-4">
    <i class="bi bi-clipboard-check"></i>
    Revisão do Pedido
</h2>

<div class="row">

<div class="col-md-7">

<div class="card mb-4">
    <div class="card-body">

        <h5 class="card-title">
            <i class="bi bi-geo-alt"></i>
            Entregar em
        </h5>

        @if($address->label)<strong>{{ $address->label }}</strong><br>@endif
        {{ $address->recipient_name }}<br>
        <span class="text-muted">{{ $address->full_address }}</span>

        <div class="mt-2">
            <a href="{{ route('store.checkout.address') }}" class="small">Trocar endereço</a>
        </div>

    </div>
</div>

<div class="card">
    <div class="card-body">

        <h5 class="card-title mb-3">
            <i class="bi bi-bag"></i>
            Itens do pedido
        </h5>

        <table class="table">
            <tbody>
                @foreach($cart as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td class="text-center">{{ $item['qty'] }}x</td>
                    <td class="text-end">R$ {{ number_format($item['price'] * $item['qty'], 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

</div>

<div class="col-md-5">

<div class="card">
    <div class="card-body">

        <h5 class="card-title mb-3">Resumo</h5>

        <div class="d-flex justify-content-between mb-2">
            <span>Subtotal</span>
            <span>R$ {{ number_format($cartTotal, 2, ',', '.') }}</span>
        </div>

        <div class="d-flex justify-content-between mb-3">
            <span>Frete</span>
            <span class="text-muted">A calcular</span>
        </div>

        <hr>

        <div class="d-flex justify-content-between mb-3">
            <strong>Total</strong>
            <strong class="fs-5 text-primary">R$ {{ number_format($cartTotal, 2, ',', '.') }}</strong>
        </div>

        <form method="POST" action="{{ route('store.checkout.place') }}">
            @csrf
            <input type="hidden" name="address_id" value="{{ $address->id }}">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-check-circle"></i>
                Confirmar Pedido
            </button>
        </form>

        <p class="text-muted small mt-3 mb-0">
            O pagamento (PIX) será habilitado em breve. Por enquanto,
            seu pedido fica registrado como "aguardando pagamento".
        </p>

    </div>
</div>

</div>

</div>

</div>

@endsection
