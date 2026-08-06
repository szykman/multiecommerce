@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Editar Regra de Frete</h2>
    <a href="{{ route('shipping-rules.index') }}" class="btn btn-secondary">Voltar</a>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
    <div class="card-body">

        <form method="POST" action="{{ route('shipping-rules.update', $rule) }}">
            @csrf
            @method('PUT')
            @include('admin.shipping_rules._form')
        </form>

    </div>
</div>

@endsection
