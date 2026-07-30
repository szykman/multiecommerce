@extends('store.layout')

@section('content')

@include('store.partials.header')

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-6">

<h2 class="mb-4">
    <i class="bi bi-credit-card"></i>
    Forma de Pagamento
</h2>

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<p class="text-muted">
    Pedido #{{ $order->id }} — Total:
    <strong>R$ {{ number_format($order->total,2,',','.') }}</strong>
</p>

@if($methods->isEmpty())

<div class="alert alert-warning">
    Esta loja ainda não configurou nenhuma forma de pagamento.
    Entre em contato para finalizar sua compra.
</div>

@else

<form method="POST" action="{{ route('store.checkout.payment.select', $order) }}">

    @csrf

    @foreach($methods as $method)

    <label class="card mb-2" style="cursor:pointer;display:block;">
        <div class="card-body d-flex align-items-center gap-3">
            <input type="radio" name="provider" value="{{ $method->provider }}" class="form-check-input" required>
            <div>
                <strong>{{ $method->label }}</strong>
                @if($method->provider === 'pix_manual')
                    <div class="text-muted small">Pague com PIX e envie o comprovante — confirmação em até algumas horas.</div>
                @endif
            </div>
        </div>
    </label>

    @endforeach

    <button type="submit" class="btn btn-primary w-100 mt-3">
        Continuar
    </button>

</form>

@endif

</div>

</div>

</div>

@endsection
