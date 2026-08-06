@php
    $rule = $rule ?? null;
@endphp

<div class="mb-3">
    <label class="form-label">Nome (exibido pro cliente)</label>
    <input
        type="text"
        name="name"
        class="form-control"
        value="{{ old('name', $rule->name ?? '') }}"
        placeholder="Ex: Retirar na loja física, Sudeste - até 50kg"
        required>
</div>

<div class="mb-3">
    <label class="form-label">Tipo</label>
    <select name="type" id="type_select" class="form-select" required>
        <option value="pickup" @selected(old('type', $rule->type ?? '')=='pickup')>Retirada (sem cálculo de região)</option>
        <option value="region" @selected(old('type', $rule->type ?? 'region')=='region')>Por região (estados + faixa de peso)</option>
    </select>
</div>

<div id="region_fields">

    <div class="mb-3">
        <label class="form-label">Estados atendidos</label>
        <div class="row">
            @foreach($states as $uf)
            <div class="col-md-2 col-4">
                <div class="form-check">
                    <input
                        type="checkbox"
                        name="states[]"
                        value="{{ $uf }}"
                        class="form-check-input"
                        id="state_{{ $uf }}"
                        {{ in_array($uf, old('states', $rule->states ?? [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="state_{{ $uf }}">{{ $uf }}</label>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="row">

        <div class="col-md-3 mb-3">
            <label class="form-label">Peso mínimo (kg)</label>
            <input
                type="number"
                step="0.001"
                name="min_weight"
                class="form-control"
                value="{{ old('min_weight', $rule->min_weight ?? 0) }}">
        </div>

        <div class="col-md-3 mb-3">
            <label class="form-label">Peso máximo (kg)</label>
            <input
                type="number"
                step="0.001"
                name="max_weight"
                class="form-control"
                value="{{ old('max_weight', $rule->max_weight ?? '') }}"
                placeholder="Sem limite">
        </div>

    </div>

</div>

<div class="row">

    <div class="col-md-3 mb-3">
        <label class="form-label">Preço (R$)</label>
        <input
            type="number"
            step="0.01"
            name="price"
            class="form-control"
            value="{{ old('price', $rule->price ?? 0) }}"
            required>
        <small class="text-muted">0 = frete grátis</small>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Prazo estimado (dias)</label>
        <input
            type="number"
            name="estimated_days"
            class="form-control"
            value="{{ old('estimated_days', $rule->estimated_days ?? '') }}">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Posição (ordem)</label>
        <input
            type="number"
            name="position"
            class="form-control"
            value="{{ old('position', $rule->position ?? 0) }}">
    </div>

</div>

<div class="form-check form-switch mb-4">
    <input
        type="checkbox"
        name="active"
        value="1"
        class="form-check-input"
        id="active"
        {{ old('active', $rule->active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="active">Ativa</label>
</div>

<button type="submit" class="btn btn-primary">
    Salvar
</button>

<a href="{{ route('shipping-rules.index') }}" class="btn btn-secondary">
    Cancelar
</a>

<script>

function togglePickupFields(){
    const isPickup = document.getElementById('type_select').value === 'pickup';
    document.getElementById('region_fields').style.display = isPickup ? 'none' : '';
}

document.getElementById('type_select').addEventListener('change', togglePickupFields);
togglePickupFields();

</script>
