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
                  <!-- PREÇOS -->

<div class="row mb-4">


    <div class="col-md-3">

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



    <div class="col-md-3">

        <label class="form-label">
            <b>Preço Promocional</b>
        </label>

        <input
            type="number"
            step="0.01"
            name="sale_price"
            class="form-control"
            value="{{ old('sale_price',$product->sale_price ?? '') }}">

    </div>



    <div class="col-md-3">

        <label class="form-label">
            <b>Início Promoção</b>
        </label>

        <input
            type="datetime-local"
            name="promotion_start"
            class="form-control"
            value="{{ old('promotion_start', optional($product->promotion_start)->format('Y-m-d\TH:i')) }}">

    </div>



    <div class="col-md-3">

        <label class="form-label">
            <b>Fim Promoção</b>
        </label>

        <input
            type="datetime-local"
            name="promotion_end"
            class="form-control"
            value="{{ old('promotion_end', optional($product->promotion_end)->format('Y-m-d\TH:i')) }}">

    </div>


</div>
<div class="mb-4">

    <label class="form-label">

        <b>Imagem do Produto</b>

    </label>

    <div id="media_preview">

        @if($product->image_url)

            <img
                id="preview_image"
                src="{{ $product->image_url }}"
                class="img-thumbnail"
                style="max-width:220px">

        @else

            <img
                id="preview_image"
                class="img-thumbnail"
                style="display:none;max-width:220px">

        @endif

    </div>

    <input
        type="file"
        name="image"
        class="form-control mb-2"
        onchange="previewLocalImage(event)">

    <button
        type="button"
        class="btn btn-outline-primary"
        onclick="openMediaPicker('image')">

        <i class="bi bi-images"></i>

        <b>Biblioteca de Mídia</b>

    </button>

    <input
        type="hidden"
        name="media_id"
        id="media_id"
        value="{{ old('media_id',$product->media_id) }}">

</div>

@include('admin.media.modal')

   <div class="mb-3">

                    <label class="form-label">

                        <b>Descrição</b>

                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="5">{{ old('description',$product->description) }}</textarea>

                </div>




                <button
                    class="btn btn-success">

                    💾 Salvar Alterações

                </button>






            </form>

        </div>

    </div>

</div>

<script>

function previewLocalImage(event){

    document.getElementById('media_id').value='';

    const file=event.target.files[0];

    if(!file){
        return;
    }

    const preview=document.getElementById('media_preview');

    preview.innerHTML='';

    const img=document.createElement('img');

    img.src=URL.createObjectURL(file);

    img.className='img-thumbnail';

    img.style.maxWidth='220px';

    preview.appendChild(img);

}

</script>

@endsection
