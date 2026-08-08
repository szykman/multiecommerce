@extends('store.layout')

@section('content')

@include('store.partials.header')

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow-sm">

<div class="card-body p-4">

<h3 class="mb-4 text-center">
    <i class="bi bi-person-plus"></i>
    Criar Conta
</h3>

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('store.register') }}">

    @csrf

    <div class="mb-3">
        <label class="form-label">Nome completo</label>
        <input
            type="text"
            name="name"
            class="form-control"
            value="{{ old('name') }}"
            required
            autofocus>
    </div>

    <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input
            type="email"
            name="email"
            class="form-control"
            value="{{ old('email') }}"
            required>
    </div>

    <div class="mb-3">
        <label class="form-label">Telefone / WhatsApp</label>

        <div class="input-group">

            <select
                name="phone_country"
                id="phone_country"
                class="form-select"
                style="max-width:170px;">

                <option value="+55" data-name="Brasil" selected>🇧🇷 +55 Brasil</option>
                <option value="+1" data-name="Estados Unidos">🇺🇸 +1 EUA</option>
                <option value="+351" data-name="Portugal">🇵🇹 +351 Portugal</option>
                <option value="+54" data-name="Argentina">🇦🇷 +54 Argentina</option>
                <option value="+595" data-name="Paraguai">🇵🇾 +595 Paraguai</option>
                <option value="+598" data-name="Uruguai">🇺🇾 +598 Uruguai</option>
                <option value="+56" data-name="Chile">🇨🇱 +56 Chile</option>
                <option value="+34" data-name="Espanha">🇪🇸 +34 Espanha</option>
                <option value="+44" data-name="Reino Unido">🇬🇧 +44 Reino Unido</option>

            </select>

            <input
                type="text"
                name="phone_number"
                id="phone_number"
                class="form-control"
                placeholder="(11) 99999-9999"
                value="{{ old('phone_number') }}">

        </div>

        <small class="text-muted">
            Usaremos este número para enviar a confirmação da compra também por WhatsApp.
        </small>

        <div id="phone_error" class="text-danger small mt-1"></div>

        <input type="hidden" name="phone" id="phone_hidden" value="{{ old('phone') }}">

    </div>

    <div class="mb-3">
        <label class="form-label">CPF ou CNPJ</label>
        <input
            type="text"
            name="document"
            id="document_input"
            class="form-control"
            placeholder="000.000.000-00"
            value="{{ old('document') }}"
            required>
        <small class="text-muted">
            Necessário para emissão de boleto e nota fiscal.
        </small>
    </div>

    <div class="row">

        <div class="col-md-6 mb-3">
            <label class="form-label">Senha</label>
            <input
                type="password"
                name="password"
                id="password_input"
                class="form-control"
                required>
            <div class="progress mt-2" style="height:6px;">
                <div id="password_strength_bar" class="progress-bar" role="progressbar" style="width:0%"></div>
            </div>
            <small id="password_strength_label" class="text-muted"></small>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Confirmar senha</label>
            <input
                type="password"
                name="password_confirmation"
                class="form-control"
                required>
        </div>

    </div>

    <button type="submit" class="btn btn-primary w-100">
        Criar Conta
    </button>

</form>

<div class="text-center mt-3">
    <span class="text-muted">Já tem conta?</span>
    <a href="{{ route('store.login') }}">Entrar</a>
</div>

</div>

</div>

</div>

</div>

</div>

<script>
@include('store.auth.partials.password-strength-script')
</script>

<script>

// Combina país + número num único campo "phone" antes do submit.
// Para o Brasil (+55), aplica máscara e exige o formato de celular:
// DDD (2 dígitos) + 9 dígitos começando com 9 -> (11) 91234-5678
const phoneCountry = document.getElementById('phone_country');
const phoneNumber = document.getElementById('phone_number');
const phoneHidden = document.getElementById('phone_hidden');
const phoneError = document.getElementById('phone_error');

function isBrazil(){
    return phoneCountry.value === '+55';
}

function maskBrazilPhone(value){

    let digits = value.replace(/\D/g, '').slice(0, 11);

    if(digits.length <= 2){
        return digits;
    }

    if(digits.length <= 7){
        return '(' + digits.slice(0,2) + ') ' + digits.slice(2);
    }

    return '(' + digits.slice(0,2) + ') ' + digits.slice(2,7) + '-' + digits.slice(7);
}

function applyPlaceholder(){
    phoneNumber.placeholder = isBrazil()
        ? '(11) 91234-5678'
        : 'Número de telefone';
}

phoneNumber.addEventListener('input', function(){

    if(isBrazil()){
        this.value = maskBrazilPhone(this.value);
    }

    phoneError.textContent = '';
});

phoneCountry.addEventListener('change', function(){
    phoneNumber.value = '';
    applyPlaceholder();
    phoneError.textContent = '';
});

applyPlaceholder();

// Máscara de CPF/CNPJ — decide qual aplicar pela quantidade de
// dígitos digitados (até 11 = CPF, 12+ = CNPJ), sem exigir que o
// cliente escolha o tipo manualmente.
const documentInput = document.getElementById('document_input');

function maskCpfCnpj(value){

    let digits = value.replace(/\D/g, '').slice(0, 14);

    if(digits.length <= 11){
        // CPF: 000.000.000-00
        return digits
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }

    // CNPJ: 00.000.000/0000-00
    return digits
        .replace(/(\d{2})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1/$2')
        .replace(/(\d{4})(\d{1,2})$/, '$1-$2');
}

documentInput.addEventListener('input', function(){
    this.value = maskCpfCnpj(this.value);
});

function updateHiddenPhone(){

    if(!phoneNumber.value.trim()){
        phoneHidden.value = '';
        return true;
    }

    if(isBrazil()){

        const digits = phoneNumber.value.replace(/\D/g, '');

        // DDD (2 dígitos) + celular com 9 dígitos, começando com 9
        if(digits.length !== 11 || digits[2] !== '9'){

            phoneError.textContent = 'Informe um celular brasileiro válido, ex: (11) 91234-5678.';
            return false;
        }

        const ddd = digits.slice(0,2);
        const rest = digits.slice(2);

        phoneHidden.value = '+55 ' + ddd + ' ' + rest.slice(0,5) + '-' + rest.slice(5);

    }else{

        phoneHidden.value = phoneCountry.value + ' ' + phoneNumber.value.trim();
    }

    return true;
}

document.querySelector('form[action="{{ route('store.register') }}"]')
    .addEventListener('submit', function(e){

        if(!updateHiddenPhone()){
            e.preventDefault();
            phoneNumber.focus();
        }

    });

</script>

@endsection
