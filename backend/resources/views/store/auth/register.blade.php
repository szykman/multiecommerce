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
        <input
            type="text"
            name="phone"
            class="form-control"
            value="{{ old('phone') }}">
    </div>

    <div class="row">

        <div class="col-md-6 mb-3">
            <label class="form-label">Senha</label>
            <input
                type="password"
                name="password"
                class="form-control"
                required>
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

@endsection
