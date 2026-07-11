@extends('admin.layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Editar Produto</h2>

        <a href="{{ route('products.index') }}"
           class="btn btn-secondary">

            Voltar

        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form method="POST"
                  action="{{ route('products.update',$product) }}"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="mb-3">

                    <label class="form-label">

                       <b>Nome</b>

                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name',$product->name) }}"
                        required>

                </div>


                <div class="mb-3">

                    <label class="form-label">

                        <b>Descrição</b>

                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="5">{{ old('description',$product->description) }}</textarea>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <div class="mb-3">

                            <label class="form-label">

                                <b>Categoria</b>

                            </label>

                            <select
                                name="category_id"
                                class="form-select">

                                <option value="">
                                    Sem categoria
                                </option>

                                @foreach($categories as $category)

                                    <option
                                        value="{{ $category->id }}"
                                        @selected(old('category_id',$product->category_id)==$category->id)>

                                        {{ $category->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="mb-3">

                            <label class="form-label">

                                <b>Preço</b>

                            </label>

                            <input
                                type="number"
                                step="0.01"
                                name="price"
                                class="form-control"
                                value="{{ old('price',$product->price) }}">

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="mb-3">

                            <label class="form-label">

                                <b>Estoque</b>

                            </label>

                            <input
                                type="number"
                                name="stock"
                                class="form-control"
                                value="{{ old('stock',$product->stock) }}">

                        </div>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6">

                        <label class="form-label">

                            <b>Nova imagem</b>

                        </label>

                        <input
                            type="file"
                            class="form-control"
                            name="image">

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">

                            <b>Status</b>

                        </label>

                        <select
                            class="form-select"
                            name="active">

                            <option
                                value="1"
                                @selected($product->active)>

                                Ativo

                            </option>

                            <option
                                value="0"
                                @selected(!$product->active)>

                                Inativo

                            </option>

                        </select>

                    </div>

                </div>


                @if($product->image)

                    <hr>

                    <div class="mb-4">

                        <label class="form-label">

                            <b>Imagem Atual</b>

                        </label>

                        <br>

                        <img
                            src="{{ asset('storage/'.$product->image) }}"
                            class="img-thumbnail"
                            style="max-width:220px;">

                    </div>

                @endif


                <button
                    class="btn btn-success">

                    💾 Salvar Alterações

                </button>

            </form>

        </div>

    </div>

</div>

@endsection
