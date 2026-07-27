@extends('store.layout')

@section('content')

@include('store.partials.header')

<div class="container py-5">

<div class="row">

<div class="col-md-3 mb-4">

<div class="list-group">
    <a href="{{ route('store.account') }}" class="list-group-item list-group-item-action">
        <i class="bi bi-person"></i>
        Minha Conta
    </a>
    <a href="{{ route('store.addresses') }}" class="list-group-item list-group-item-action active">
        <i class="bi bi-geo-alt"></i>
        Meus Endereços
    </a>
    <a href="{{ route('store.orders') }}" class="list-group-item list-group-item-action">
        <i class="bi bi-bag"></i>
        Meus Pedidos
    </a>
</div>

</div>

<div class="col-md-9">

<h3 class="mb-4">
    <i class="bi bi-geo-alt"></i>
    Meus Endereços
</h3>

@foreach($addresses as $addr)

<div class="card mb-2">
    <div class="card-body d-flex justify-content-between align-items-start">

        <div>

            @if($addr->is_default)
                <span class="badge bg-primary mb-1">Padrão</span>
            @endif

            @if($addr->label)
                <span class="badge bg-secondary mb-1">{{ $addr->label }}</span>
            @endif

            <p class="mb-1"><strong>{{ $addr->recipient_name }}</strong></p>
            <p class="mb-1 text-muted small">
                {{ $addr->street }}, {{ $addr->number }}{{ $addr->complement ? ' - '.$addr->complement : '' }}<br>
                {{ $addr->neighborhood }} — {{ $addr->city }}/{{ $addr->state }} — CEP {{ $addr->zipcode }}
            </p>

        </div>

        <form
            method="POST"
            action="{{ route('store.addresses.destroy', $addr) }}"
            onsubmit="return confirm('Remover este endereço?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash"></i>
            </button>
        </form>

    </div>
</div>

@endforeach

@if($addresses->isEmpty())
<div class="alert alert-info">
    Você ainda não tem endereços cadastrados.
</div>
@endif

<hr class="my-4">

<h5 class="mb-3">Adicionar novo endereço</h5>

<form method="POST" action="{{ route('store.checkout.address.store') }}">

    @csrf

    <input type="hidden" name="context" value="account">

    <div class="mb-3">
        <label class="form-label">Apelido (opcional)</label>
        <input type="text" name="label" class="form-control" placeholder="Casa, Trabalho...">
    </div>

    <div class="mb-3">
        <label class="form-label">Nome do destinatário</label>
        <input type="text" name="recipient_name" class="form-control" required>
    </div>

    <div class="row">

        <div class="col-md-4 mb-3">
            <label class="form-label">CEP</label>
            <input type="text" name="zipcode" id="zipcode_input" class="form-control" maxlength="9" required>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Rua</label>
            <input type="text" name="street" id="street_input" class="form-control" required>
        </div>

        <div class="col-md-2 mb-3">
            <label class="form-label">Número</label>
            <input type="text" name="number" class="form-control" required>
        </div>

    </div>

    <div class="mb-3">
        <label class="form-label">Complemento</label>
        <input type="text" name="complement" class="form-control">
    </div>

    <div class="row">

        <div class="col-md-5 mb-3">
            <label class="form-label">Bairro</label>
            <input type="text" name="neighborhood" id="neighborhood_input" class="form-control" required>
        </div>

        <div class="col-md-5 mb-3">
            <label class="form-label">Cidade</label>
            <input type="text" name="city" id="city_input" class="form-control" required>
        </div>

        <div class="col-md-2 mb-3">
            <label class="form-label">UF</label>
            <input type="text" name="state" id="state_input" class="form-control" maxlength="2" style="text-transform:uppercase" required>
        </div>

    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="is_default" value="1" class="form-check-input" id="is_default">
        <label class="form-check-label" for="is_default">Usar como endereço padrão</label>
    </div>

    <button type="submit" class="btn btn-success">
        Salvar endereço
    </button>

</form>

</div>

</div>

</div>

<script>

document.getElementById('zipcode_input').addEventListener('blur', function(){

    const cep = this.value.replace(/\D/g, '');

    if(cep.length !== 8){
        return;
    }

    fetch('https://viacep.com.br/ws/' + cep + '/json/')
        .then(function(r){ return r.json(); })
        .then(function(data){

            if(data.erro){
                return;
            }

            document.getElementById('street_input').value = data.logradouro || '';
            document.getElementById('neighborhood_input').value = data.bairro || '';
            document.getElementById('city_input').value = data.localidade || '';
            document.getElementById('state_input').value = data.uf || '';

        })
        .catch(function(err){
            console.error('Erro ao buscar CEP:', err);
        });

});

</script>

@endsection
