@extends('store.layout')

@section('content')

@include('store.partials.header')

<div class="container py-5">

<div class="row">

<div class="col-md-3 mb-4">

<div class="list-group">
    <a href="{{ route('store.account') }}" class="list-group-item list-group-item-action active">
        <i class="bi bi-person"></i>
        Minha Conta
    </a>
    <a href="{{ route('store.addresses') }}" class="list-group-item list-group-item-action">
        <i class="bi bi-geo-alt"></i>
        Meus Endereços
    </a>
    <a href="{{ route('store.orders') }}" class="list-group-item list-group-item-action">
        <i class="bi bi-bag"></i>
        Meus Pedidos
    </a>
</div>

<form
    method="POST"
    action="{{ route('store.logout') }}"
    class="mt-3">
    @csrf
    <button type="submit" class="btn btn-outline-danger w-100">
        <i class="bi bi-box-arrow-right"></i>
        Sair
    </button>
</form>

</div>

<div class="col-md-9">

<div class="card shadow-sm mb-4">

<div class="card-body p-4">

<h3 class="mb-4">
    <i class="bi bi-person-circle"></i>
    Meus Dados
</h3>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    @foreach($errors->all() as $error)
        {{ $error }}
    @endforeach
</div>
@endif

<form method="POST" action="{{ route('store.account.update') }}">

    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Nome</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input type="email" class="form-control" value="{{ $customer->email }}" disabled>
        <small class="text-muted">Para trocar o e-mail, entre em contato com a loja.</small>
    </div>

    <div class="mb-3">
        <label class="form-label">Telefone / WhatsApp</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}" placeholder="+55 11 91234-5678">
    </div>

    <button type="submit" class="btn btn-primary">
        Salvar alterações
    </button>

</form>

<hr>

<p class="text-muted small mb-0">
    Cliente desde {{ $customer->created_at->format('d/m/Y') }} —
    <a href="{{ route('store.password.request') }}">Alterar minha senha</a>
</p>

</div>

</div>

<div class="card shadow-sm">

<div class="card-body p-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">
        <i class="bi bi-geo-alt"></i>
        Endereços
    </h5>
    <a href="{{ route('store.addresses') }}" class="btn btn-outline-secondary btn-sm">
        Gerenciar endereços
    </a>
</div>

@forelse($addresses->take(2) as $addr)

<div class="border-bottom pb-2 mb-2">
    @if($addr->is_default)<span class="badge bg-primary mb-1">Padrão</span>@endif
    <p class="mb-0 small">
        <strong>{{ $addr->recipient_name }}</strong><br>
        {{ $addr->street }}, {{ $addr->number }} — {{ $addr->city }}/{{ $addr->state }}
    </p>
</div>

@empty

<p class="text-muted small mb-0">
    Nenhum endereço cadastrado ainda.
    <a href="{{ route('store.addresses') }}">Cadastrar agora</a>
</p>

@endforelse

@if($addresses->count() > 2)
<p class="text-muted small mb-0">
    + {{ $addresses->count() - 2 }} outro(s) endereço(s) —
    <a href="{{ route('store.addresses') }}">ver todos</a>
</p>
@endif

</div>

</div>

</div>

</div>

</div>

@endsection
