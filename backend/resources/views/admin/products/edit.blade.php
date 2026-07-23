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

        <b>Imagem Principal</b>

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





<hr class="my-4">

<h4>
    <i class="bi bi-images"></i>
    Galeria de Fotos
</h4>

<p class="text-muted">
    Adicione fotos adicionais do produto.
</p>

<div class="mb-3">

    <button
        type="button"
        class="btn btn-outline-primary"
        onclick="openGalleryPicker()">

        <i class="bi bi-images"></i>

        Biblioteca de Mídia

    </button>

<input
    type="file"
    id="gallery_upload"
    class="form-control mt-2"
    multiple>

<div id="gallery_upload_preview" class="row g-3 mt-3"></div>

{{--
    Aqui ficam os hidden inputs "gallery[]" das fotos NOVAS
    (upload direto ou selecionadas na biblioteca).
--}}
<div id="gallery_inputs"></div>




{{--
    Fotos JÁ SALVAS no produto. Cada card agora carrega seu
    próprio hidden input "gallery[]" dentro do mesmo elemento
    removido pelo botão "Excluir" — assim o array enviado no
    submit representa sempre o estado final da galeria
    (existentes que não foram excluídas + novas adicionadas),
    e não some mais nada que já estava salvo.
--}}
<div
    id="gallery_list"
    class="row g-3">


@foreach($product->gallery as $photo)

<div class="col-md-3 gallery-item"
     data-media="{{ $photo->media_id }}">

    <div class="card">

        <img
            src="{{ asset('storage/'.$photo->media->file) }}"
            class="card-img-top"
            style="height:180px;object-fit:cover">

        <div class="card-body text-center">

            <button
                type="button"
                class="btn btn-danger btn-sm btn-remove-gallery">

                Excluir

            </button>

        </div>

    </div>

    <input
        type="hidden"
        name="gallery[]"
        value="{{ $photo->media_id }}">

</div>

@endforeach

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


<script>

// Remove um card da galeria (existente ou recém-adicionado)
// junto com o hidden input "gallery[]" correspondente.
function removeGalleryItem(button){

    let col = button.closest('.col-md-3');

    if(col){
        col.remove();
    }

}

</script>


<script>

document.getElementById('gallery_upload').addEventListener('change', function(e){

    let files = e.target.files;
    if(!files.length){
        return;
    }

    let preview = document.getElementById('gallery_upload_preview');
    let inputs = document.getElementById('gallery_inputs');
    let formData = new FormData();

    preview.innerHTML = '';

    for(let i = 0; i < files.length; i++){

        formData.append('files[]', files[i]);

        let img = document.createElement('img');
        img.src = URL.createObjectURL(files[i]);
        img.className = 'img-thumbnail';
        img.style.width = '180px';
        img.style.height = '180px';
        img.style.objectFit = 'cover';

        let col = document.createElement('div');
        col.className = 'col-md-3';
        col.appendChild(img);

        preview.appendChild(col);
    }

    fetch("{{ route('media.upload') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {

        if(!data.success){
            alert('Erro no upload');
            return;
        }

        if(data.media){

            let html = '';

            data.media.forEach(function(item){
                html += `<input type="hidden" name="gallery[]" value="${item.id}">`;
            });

            // += para NÃO apagar os hidden inputs já existentes
            // (fotos salvas anteriormente + outros lotes de upload)
            inputs.innerHTML += html;

        }

    })
    .catch(function(err){
        console.error('Erro na requisição de upload:', err);
        alert('Falha na comunicação com o servidor.');
    });

});

</script>

<script>

document.addEventListener('click', function(e){

    if(!e.target.classList.contains('btn-remove-gallery')){
        return;
    }

    let item = e.target.closest('.gallery-item');

    if(!item){
        return;
    }

    item.remove();

});

</script>

@endsection
