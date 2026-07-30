@extends('store.layout')

@section('content')

@include('store.partials.header')

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="text-center mb-4">
    <i class="bi bi-qr-code display-4 text-primary"></i>
    <h2 class="mt-2">Pague com PIX</h2>
    <p class="text-muted">Pedido #{{ $order->id }}</p>
</div>

<div class="card shadow-sm">
    <div class="card-body text-center p-4">

        <h3 class="text-primary mb-3">
            R$ {{ number_format($payment->amount, 2, ',', '.') }}
        </h3>

        <div id="qrcode" class="d-flex justify-content-center mb-3"></div>

        <p class="text-muted small mb-3">
            Escaneie o QR code no app do seu banco, ou copie o código abaixo.
        </p>

        <div class="input-group mb-3">
            <input
                type="text"
                id="copy_paste_input"
                class="form-control form-control-sm"
                value="{{ $payment->raw_response['copy_paste'] ?? '' }}"
                readonly>
            <button class="btn btn-outline-secondary btn-sm" type="button" id="copy_btn">
                Copiar
            </button>
        </div>

        <p class="text-muted small">
            Chave PIX: <strong>{{ $payment->raw_response['pix_key'] ?? '' }}</strong><br>
            Titular: {{ $payment->raw_response['holder_name'] ?? '' }}
        </p>

        <hr>

        @if($payment->status === 'awaiting_confirmation')

        <div class="alert alert-warning mb-0">
            <i class="bi bi-hourglass-split"></i>
            Avisamos a loja que você já pagou. Aguarde a confirmação.
        </div>

        @else

        <form method="POST" action="{{ route('store.checkout.payment.confirm', $order) }}">
            @csrf
            <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-check-circle"></i>
                Já paguei
            </button>
        </form>

        @endif

    </div>
</div>

<p class="text-center text-muted small mt-3">
    Depois de pagar, clique em "Já paguei" para avisar a loja.
    O pedido será confirmado assim que o pagamento for conferido.
</p>

</div>

</div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>

new QRCode(document.getElementById('qrcode'), {
    text: document.getElementById('copy_paste_input').value,
    width: 220,
    height: 220,
});

document.getElementById('copy_btn').addEventListener('click', function(){

    const input = document.getElementById('copy_paste_input');
    input.select();
    input.setSelectionRange(0, 99999);

    navigator.clipboard.writeText(input.value).then(function(){
        const btn = document.getElementById('copy_btn');
        const original = btn.textContent;
        btn.textContent = 'Copiado!';
        setTimeout(function(){ btn.textContent = original; }, 2000);
    });

});

</script>

@endsection
