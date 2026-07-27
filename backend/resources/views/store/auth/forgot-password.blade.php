@extends('store.layout')

@section('content')

@include('store.partials.header')

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow-sm">

<div class="card-body p-4">

<h3 class="mb-3 text-center">
    <i class="bi bi-key"></i>
    Esqueci minha senha
</h3>

<p class="text-muted text-center">
    Informe seu e-mail e enviaremos um link para redefinir sua senha.
</p>

@if($errors->any())
<div class="alert alert-danger">
    @foreach($errors->all() as $error)
        {{ $error }}
    @endforeach
</div>
@endif

<form method="POST" action="{{ route('store.password.email') }}">

    @csrf

    <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
    </div>

    <button type="submit" class="btn btn-primary w-100">
        Enviar link de redefinição
    </button>

</form>

<div class="text-center mt-3">
    <a href="{{ route('store.login') }}">Voltar ao login</a>
</div>

</div>

</div>

</div>

</div>

</div>

@endsection
