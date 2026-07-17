@extends('admin.layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Nova Categoria</h2>

        <a href="{{ route('categories.index') }}"
           class="btn btn-secondary">

            Voltar

        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form method="POST"
                  action="{{ route('categories.store') }}">

                @csrf

                <div class="mb-3">

                    <label class="form-label">

                      <strong>Nome da Categoria</strong>

                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        required>

                    @error('name')

                        <div class="text-danger mt-2">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

<div class="mb-3">

<label class="form-label">

<strong>Tipo</strong>

</label>

<select
name="type"
class="form-select">

<option value="store">

Loja Virtual

</option>

<option value="cms">

Página CMS

</option>

</select>

</div>

                <button
                    class="btn btn-success">

                    💾 Salvar Categoria

                </button>

            </form>

        </div>

    </div>

</div>

@endsection
