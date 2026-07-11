@extends('admin.layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Editar Categoria</h2>

        <a href="{{ route('categories.index') }}"
           class="btn btn-secondary">

            Voltar

        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form method="POST"
                  action="{{ route('categories.update', $category) }}">

                @csrf
                @method('PUT')

                <div class="mb-3">

                    <label class="form-label">

                      <b>Nome da Categoria</b>

                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name', $category->name) }}"
                        required>

                    @error('name')

                        <div class="text-danger mt-2">

                            {{ $message }}

                        </div>

                    @enderror

                </div>

                <div class="mb-4">

                    <label class="form-label">

                       <b>Status</b>
 
                    </label>

                    <select
                        name="active"
                        class="form-select">

                        <option value="1"
                            @selected($category->active)>
                            Ativa
                        </option>

                        <option value="0"
                            @selected(!$category->active)>
                            Inativa
                        </option>

                    </select>

                </div>

                <button
                    type="submit"
                    class="btn btn-success">

                    💾 Salvar Alterações

                </button>

            </form>

        </div>

    </div>

</div>

@endsection
