@extends('store.layout')

@section('content')

<div class="container py-5">

<h1 class="mb-4">
    <i class="bi bi-cart3"></i>
    Carrinho
</h1>

@if($cartItems->count())

<div class="table-responsive">

    <table class="table align-middle">

        <thead>
            <tr>
                <th style="width:90px"></th>
                <th>Produto</th>
                <th style="width:160px">Quantidade</th>
                <th style="width:140px" class="text-end">Preço unit.</th>
                <th style="width:140px" class="text-end">Subtotal</th>
                <th style="width:60px"></th>
            </tr>
        </thead>

        <tbody>

            @foreach($cartItems as $item)

            <tr>

                <td>
                    @if($item['image'])
                        <img
                            src="{{ $item['image'] }}"
                            width="70"
                            height="70"
                            style="object-fit:cover;border-radius:8px;">
                    @endif
                </td>

                <td>

                    @if($item['slug'])
                        <a href="{{ route('store.product', $item['slug']) }}" class="text-decoration-none text-dark">
                            <strong>{{ $item['name'] }}</strong>
                        </a>
                    @else
                        <strong>{{ $item['name'] }}</strong>
                    @endif

                    @if(!$item['available'])
                        <div class="text-danger small mt-1">
                            <i class="bi bi-exclamation-triangle"></i>
                            Este item não está mais disponível.
                        </div>
                    @elseif($item['exceeds_stock'])
                        <div class="text-warning small mt-1">
                            <i class="bi bi-exclamation-triangle"></i>
                            Apenas {{ $item['current_stock'] }} em estoque — ajuste a quantidade.
                        </div>
                    @endif

                </td>

                <td>
                    <form
                        method="POST"
                        action="{{ route('store.cart.update', $item['key']) }}"
                        class="d-flex align-items-center gap-1 cart-qty-form">

                        @csrf

                        <input
                            type="number"
                            name="quantity"
                            value="{{ $item['qty'] }}"
                            min="1"
                            max="{{ max(1, $item['current_stock']) }}"
                            class="form-control form-control-sm"
                            style="width:70px;">

                        <button type="submit" class="btn btn-outline-secondary btn-sm" title="Atualizar quantidade">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>

                    </form>
                </td>

                <td class="text-end">
                    R$ {{ number_format($item['price'],2,',','.') }}
                </td>

                <td class="text-end">
                    <strong>R$ {{ number_format($item['subtotal'],2,',','.') }}</strong>
                </td>

                <td>
                    <form
                        method="POST"
                        action="{{ route('store.cart.remove', $item['key']) }}"
                        onsubmit="return confirm('Remover este item do carrinho?')">

                        @csrf

                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Remover">
                            <i class="bi bi-trash"></i>
                        </button>

                    </form>
                </td>

            </tr>

            @endforeach

        </tbody>

        <tfoot>
            <tr>
                <td colspan="4" class="text-end">
                    <strong>Total</strong>
                </td>
                <td class="text-end">
                    <strong class="fs-5 text-primary">
                        R$ {{ number_format($cartTotal,2,',','.') }}
                    </strong>
                </td>
                <td></td>
            </tr>
        </tfoot>

    </table>

</div>

<div class="d-flex justify-content-between align-items-center mt-4">

    <a href="{{ url('/') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
        Continuar comprando
    </a>

    <div class="d-flex gap-2">

        <form
            method="POST"
            action="{{ route('store.cart.clear') }}"
            onsubmit="return confirm('Esvaziar o carrinho inteiro?')">

            @csrf

            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-x-circle"></i>
                Esvaziar carrinho
            </button>

        </form>

        <button type="button" class="btn btn-primary" disabled title="Em breve">
            <i class="bi bi-bag-check"></i>
            Finalizar Compra
        </button>

    </div>

</div>

@else

<div class="alert alert-info">
    <i class="bi bi-cart-x"></i>
    Seu carrinho está vazio.
</div>

<a href="{{ url('/') }}" class="btn btn-primary">
    <i class="bi bi-shop"></i>
    Ver produtos
</a>

@endif

</div>

<script>

// Envia o formulário de quantidade automaticamente ao alterar o
// valor do input, sem precisar clicar no botão de atualizar.
document.querySelectorAll('.cart-qty-form input[name="quantity"]').forEach(function(input){

    input.addEventListener('change', function(){
        this.closest('form').submit();
    });

});

</script>

@endsection
