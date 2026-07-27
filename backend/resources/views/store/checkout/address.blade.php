@extends('store.layout')

@section('content')

@include('store.partials.header')

<div class="container py-5">

<h2 class="mb-4">
    <i class="bi bi-geo-alt"></i>
    Endereço de Entrega
</h2>

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row">

<div class="col-md-6">

@if($addresses->count())

<h5 class="mb-3">Endereços salvos</h5>

<form method="GET" action="{{ route('store.checkout.review') }}">

    @foreach($addresses as $addr)

    <label
        for="addr_{{ $addr->id }}"
        class="card mb-2"
        style="cursor:pointer;display:block;">
        <div class="card-body">
            <div class="form-check">
                <input
                    class="form-check-input"
                    type="radio"
                    name="address_id"
                    value="{{ $addr->id }}"
                    id="addr_{{ $addr->id }}"
                    {{ $addr->is_default ? 'checked' : '' }}>

                @if($addr->label)
                    <span class="badge bg-secondary mb-1">{{ $addr->label }}</span>
                @endif

                <table class="table table-sm table-borderless mb-0 mt-1">
                    <tr>
                        <td class="text-muted" style="width:110px">Destinatário</td>
                        <td><strong>{{ $addr->recipient_name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Endereço</td>
                        <td>{{ $addr->street }}, {{ $addr->number }}{{ $addr->complement ? ' - '.$addr->complement : '' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Bairro/Cidade</td>
                        <td>{{ $addr->neighborhood }} — {{ $addr->city }}/{{ $addr->state }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">CEP</td>
                        <td>{{ $addr->zipcode }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </label>

    @endforeach

    <button type="submit" class="btn btn-primary mt-2">
        Continuar com este endereço
    </button>

</form>

<hr class="my-4">

@endif

<h5 class="mb-3">
    {{ $addresses->count() ? 'Ou cadastre um novo endereço' : 'Cadastre seu endereço de entrega' }}
</h5>

<form method="POST" action="{{ route('store.checkout.address.store') }}">

    @csrf

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
        Salvar e continuar
    </button>

</form>

</div>

</div>

</div>

<script>

// Auto-preenchimento via ViaCEP (gratuito, sem necessidade de chave)
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
