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
    <span class="list-group-item text-muted">
        <i class="bi bi-bag"></i>
        Meus Pedidos
        <span class="badge bg-secondary float-end">em breve</span>
    </span>
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

<div class="card shadow-sm">

<div class="card-body p-4">

<h3 class="mb-4">
    <i class="bi bi-person-circle"></i>
    Olá, {{ $customer->name }}
</h3>

<dl class="row mb-0">

    <dt class="col-sm-3">Nome</dt>
    <dd class="col-sm-9">{{ $customer->name }}</dd>

    <dt class="col-sm-3">E-mail</dt>
    <dd class="col-sm-9">{{ $customer->email }}</dd>

    <dt class="col-sm-3">Telefone</dt>
    <dd class="col-sm-9">{{ $customer->phone ?: '—' }}</dd>

    <dt class="col-sm-3">Cliente desde</dt>
    <dd class="col-sm-9">{{ $customer->created_at->format('d/m/Y') }}</dd>

</dl>

</div>

</div>

</div>

</div>

</div>

@endsection
