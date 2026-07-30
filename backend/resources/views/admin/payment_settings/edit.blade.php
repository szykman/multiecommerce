@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Formas de Pagamento</h2>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card mb-4">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">
                <i class="bi bi-qr-code"></i>
                PIX (chave manual)
            </h5>
            <span class="badge bg-success">Disponível</span>
        </div>

        <p class="text-muted small">
            O cliente recebe o QR code/copia-e-cola no checkout e paga
            direto na sua chave PIX. A confirmação é manual: você
            confere no extrato do seu banco e confirma o pedido aqui
            no admin.
        </p>

        <form method="POST" action="{{ route('payment-settings.update') }}">

            @csrf
            @method('PUT')

            <div class="form-check form-switch mb-3">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="enabled"
                    id="enabled"
                    value="1"
                    {{ $pixMethod->enabled ? 'checked' : '' }}>
                <label class="form-check-label" for="enabled">
                    Ativar PIX manual nesta loja
                </label>
            </div>

            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="form-label">Tipo de chave</label>
                    <select name="pix_key_type" class="form-select">
                        <option value="cpf" @selected(($pixMethod->credentials['pix_key_type'] ?? '')=='cpf')>CPF</option>
                        <option value="cnpj" @selected(($pixMethod->credentials['pix_key_type'] ?? '')=='cnpj')>CNPJ</option>
                        <option value="email" @selected(($pixMethod->credentials['pix_key_type'] ?? '')=='email')>E-mail</option>
                        <option value="phone" @selected(($pixMethod->credentials['pix_key_type'] ?? '')=='phone')>Telefone</option>
                        <option value="random" @selected(($pixMethod->credentials['pix_key_type'] ?? '')=='random')>Chave aleatória</option>
                    </select>
                </div>

                <div class="col-md-8 mb-3">
                    <label class="form-label">Chave PIX</label>
                    <input
                        type="text"
                        name="pix_key"
                        class="form-control"
                        value="{{ old('pix_key', $pixMethod->credentials['pix_key'] ?? '') }}"
                        required>
                </div>

            </div>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Nome do titular</label>
                    <input
                        type="text"
                        name="holder_name"
                        class="form-control"
                        value="{{ old('holder_name', $pixMethod->credentials['holder_name'] ?? '') }}"
                        required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Cidade (do titular)</label>
                    <input
                        type="text"
                        name="city"
                        class="form-control"
                        value="{{ old('city', $pixMethod->credentials['city'] ?? '') }}"
                        required>
                </div>

            </div>

            <button type="submit" class="btn btn-primary">
                Salvar
            </button>

        </form>

    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Mercado Pago</h6>
            <span class="badge bg-secondary">Em breve</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">PagSeguro</h6>
            <span class="badge bg-secondary">Em breve</span>
        </div>
    </div>
</div>

@endsection
