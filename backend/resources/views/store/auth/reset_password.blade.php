@extends('store.layout')

@section('content')

@include('store.partials.header')

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow-sm">

<div class="card-body p-4">

<h3 class="mb-4 text-center">
    <i class="bi bi-key"></i>
    Redefinir Senha
</h3>

@if($errors->any())
<div class="alert alert-danger">
    @foreach($errors->all() as $error)
        {{ $error }}
    @endforeach
</div>
@endif

<form method="POST" action="{{ route('store.password.update') }}">

    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $email) }}" required autofocus>
    </div>

    <div class="mb-3">
        <label class="form-label">Nova senha</label>
        <input type="password" name="password" id="password_input" class="form-control" required>
        <div class="progress mt-2" style="height:6px;">
            <div id="password_strength_bar" class="progress-bar" role="progressbar" style="width:0%"></div>
        </div>
        <small id="password_strength_label" class="text-muted"></small>
    </div>

    <div class="mb-3">
        <label class="form-label">Confirmar nova senha</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">
        Redefinir Senha
    </button>

</form>

</div>

</div>

</div>

</div>

</div>

<script>
@include('store.auth.partials.password-strength-script')
</script>

@endsection
