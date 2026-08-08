@extends('store.layout')

@section('content')

@include('store.partials.header')

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-6">

@php
    // pay.blade.php até aqui só sabia mostrar PIX (QR code gerado a
    // partir do copy_paste, chave PIX etc.) — qualquer outro provider
    // caía nessa mesma tela e ficava com informação errada (boleto
    // virando "QR code" ilegível, por exemplo). Agora ramifica pelo
    // tipo real de cobrança.
    $isBoleto = $payment->provider === 'mercadopago_boleto';
@endphp

<div class="text-center mb-4">
    <i class="bi {{ $isBoleto ? 'bi-upc-scan' : 'bi-qr-code' }} display-4 text-primary"></i>
    <h2 class="mt-2">{{ $isBoleto ? 'Pague com Boleto' : 'Pague com PIX' }}</h2>
    <p class="text-muted">Pedido #{{ $order->id }}</p>
</div>

<div class="card shadow-sm">
    <div class="card-body text-center p-4">

        <h3 class="text-primary mb-3">
            R$ {{ number_format($payment->amount, 2, ',', '.') }}
        </h3>

        @if($isBoleto)

        @if(! empty($payment->raw_response['boleto_url']))
        <a
            href="{{ $payment->raw_response['boleto_url'] }}"
            target="_blank"
            rel="noopener"
            class="btn btn-primary w-100 mb-3">
            <i class="bi bi-file-earmark-text"></i>
            Visualizar / imprimir boleto
        </a>
        @endif

        <p class="text-muted small mb-3">
            Ou copie a linha digitável abaixo no app do seu banco.
        </p>

        @else

        <div id="qrcode" class="d-flex justify-content-center mb-3"></div>

        <p class="text-muted small mb-3">
            Escaneie o QR code no app do seu banco, ou copie o código abaixo.
        </p>

        @endif

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

        @if(! $isBoleto)
        <p class="text-muted small">
            Chave PIX: <strong>{{ $payment->raw_response['pix_key'] ?? '' }}</strong><br>
            Titular: {{ $payment->raw_response['holder_name'] ?? '' }}
        </p>
        @endif

        <hr>

        @php
            // PIX manual depende do cliente avisar "já paguei" e do
            // lojista confirmar. Qualquer outro provider (Mercado
            // Pago, e os que vierem depois) confirma sozinho via
            // webhook — não faz sentido pedir pro cliente clicar
            // em nada, só esperar.
            $isAutoConfirm = $payment->provider !== 'pix_manual';
        @endphp

        @if($isAutoConfirm)

        <div id="auto_confirm_waiting" class="alert alert-info mb-0">
            <i class="bi bi-arrow-repeat"></i>
            Aguardando confirmação automática do pagamento...
        </div>

        @elseif($payment->status === 'awaiting_confirmation')

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
    @if($isAutoConfirm)
        Assim que o pagamento cair, esta página atualiza sozinha —
        não precisa clicar em nada.
    @else
        Depois de pagar, clique em "Já paguei" para avisar a loja.
        O pedido será confirmado assim que o pagamento for conferido.
    @endif
</p>

</div>

</div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>

@if(! $isBoleto)
new QRCode(document.getElementById('qrcode'), {
    text: document.getElementById('copy_paste_input').value,
    width: 220,
    height: 220,
});
@endif

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

@if($isAutoConfirm)
<script>

// Verifica a cada 4 segundos se o pedido já foi confirmado
// automaticamente (webhook do gateway) — sem exigir ação do cliente.
const statusUrl = "{{ route('store.checkout.payment.status', $order) }}";
const confirmationUrl = "{{ route('store.checkout.confirmation', $order) }}";

const pollInterval = setInterval(function(){

    fetch(statusUrl)
        .then(function(r){ return r.json(); })
        .then(function(data){

            if(data.status === 'paid'){
                clearInterval(pollInterval);
                window.location.href = confirmationUrl;
            }

        })
        .catch(function(err){
            console.error('Erro ao verificar status do pagamento:', err);
        });

}, 4000);

</script>
@endif

@endsection
