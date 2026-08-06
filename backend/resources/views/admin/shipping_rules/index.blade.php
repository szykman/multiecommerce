@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Frete Personalizado</h2>

    <a href="{{ route('shipping-rules.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        Nova Regra
    </a>

</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($rules->count())

<div class="table-responsive">

    <table class="table align-middle">

        <thead>
            <tr>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Estados</th>
                <th>Peso (kg)</th>
                <th class="text-end">Preço</th>
                <th class="text-center">Ativa</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            @foreach($rules as $rule)
            <tr>
                <td>{{ $rule->name }}</td>
                <td>{{ $rule->type === 'pickup' ? 'Retirada' : 'Por região' }}</td>
                <td>{{ $rule->type === 'pickup' ? '—' : implode(', ', $rule->states ?? []) }}</td>
                <td>
                    {{ $rule->min_weight }}
                    até
                    {{ $rule->max_weight ?? '∞' }}
                </td>
                <td class="text-end">
                    {{ $rule->price > 0 ? 'R$ '.number_format($rule->price,2,',','.') : 'Grátis' }}
                </td>
                <td class="text-center">
                    @if($rule->active)
                        <span class="badge bg-success">Sim</span>
                    @else
                        <span class="badge bg-secondary">Não</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('shipping-rules.edit', $rule) }}" class="btn btn-outline-secondary btn-sm">
                        Editar
                    </a>
                    <form method="POST" action="{{ route('shipping-rules.destroy', $rule) }}" class="d-inline" onsubmit="return confirm('Remover esta regra?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>

</div>

@else

<div class="alert alert-info">
    Nenhuma regra de frete cadastrada ainda. Clique em "Nova Regra" para criar
    (ex: "Retirar na loja física" grátis, ou um valor fixo por região).
</div>

@endif

@endsection
