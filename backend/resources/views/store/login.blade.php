@extends('store.layout')

@section('content')

@include('store.partials.header')

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow-sm">

<div class="card-body p-4">

<h3 class="mb-4 text-center">
    <i class="bi bi-person-circle"></i>
    Entrar
</h3>

@if($errors->any())
<div class="alert alert-danger">
    @foreach($errors->all() as $error)
        {{ $error }}
    @endforeach
</div>
@endif

<form method="POST" action="{{ route('store.login') }}">

    @csrf

    <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input
            type="email"
            name="email"
            class="form-control"
            value="{{ old('email') }}"
            required
            autofocus>
    </div>

    <div class="mb-3">
        <label class="form-label">Senha</label>
        <input
            type="password"
            name="password"
            class="form-control"
            required>
    </div>

    <div class="form-check mb-3">
        <input
            type="checkbox"
            name="remember"
            value="1"
            class="form-check-input"
            id="remember">
        <label class="form-check-label" for="remember">
            Lembrar de mim
        </label>
    </div>

    <button type="submit" class="btn btn-primary w-100">
        Entrar
    </button>

</form>

<div class="text-center mt-3">
    <a href="{{ route('store.password.request') }}" class="small">Esqueci minha senha</a>
</div>

<div class="text-center mt-2">
    <span class="text-muted">Ainda não tem conta?</span>
    <a href="{{ route('store.register') }}">Cadastre-se</a>
</div>

</div>

</div>

</div>

</div>

</div>

@endsection
