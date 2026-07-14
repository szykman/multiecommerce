@extends('admin.layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Novo Produto</h2>

        <a href="{{ route('products.index') }}"
           class="btn btn-secondary">

            Voltar

        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form method="POST"
                  action="{{ route('products.store') }}"
                  enctype="multipart/form-data">

                @csrf

                <div class="mb-3">

                    <label class="form-label">

                      <b>Nome</b>

                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                       <b>Descrição</b>

                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="5">{{ old('description') }}</textarea>

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

                                    <option value="{{ $category->id }}">

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
				style="width:200px;"
                                type="number"
                                step="0.01"
                                name="price"
                                class="form-control"
                                required>

                        </div>

<div class="row">


<div>

</div>


<div class="col-md-4">

<label>Preço Promocional</label>

<input 
style="width:200px;"
type="number"
step="0.01"
name="sale_price"
class="form-control"
value="{{ old('sale_price',$product->sale_price ?? '') }}">

</div>


<div>

</div>


<div class="col-md-4">

<label>Início Promoção</label>

<input 
style="width:200px;"
type="datetime-local"
name="promotion_start"
class="form-control"
value="{{ old('promotion_start') }}">
</div>


<div>

</div>

<div class="col-md-4 mt-3">

<label>Fim Promoção</label>

<input 
style="width:200px;"
type="datetime-local"
name="promotion_end"
class="form-control"
value="{{ old('promotion_end') }}">
</div>

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
                                value="0"
                                class="form-control"
                                required>

                        </div>

                    </div>

                </div>

                <div class="mb-4">

                    <label class="form-label">

                       <b>Imagem</b>

                    </label>

                    <input
                        type="file"
                        name="image"
                        class="form-control">

                </div>

                <button
                    type="submit"
                    class="btn btn-success">

                    💾 Salvar Produto

                </button>

            </form>

        </div>

    </div>

</div>

@endsection
