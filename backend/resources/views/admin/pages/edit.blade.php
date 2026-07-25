@extends('admin.layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Editar Página</h2>

        <a
            href="{{ route('pages.index') }}"
            class="btn btn-secondary">

            Voltar

        </a>

    </div>

    <form
        method="POST"
        action="{{ route('pages.update',$page) }}"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="card">

            <div class="card-body">

                <div class="mb-3">

                    <label class="form-label">

<b>                        Categoria CMS</b>

                    </label>

                    <select
                        name="category_id"
                        class="form-select"
                        required>

                        <option value="">

                            Selecione

                        </option>

                        @foreach($categories as $category)

                            <option
                                value="{{ $category->id }}"
                                {{ $page->category_id == $category->id ? 'selected' : '' }}>

                                {{ $category->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        <b>Título</b>

                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name',$page->name) }}"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                       <b>Imagem de Capa</b>

                    </label>

<div id="media_preview">

@if($page->image_thumbnail_url)

<img
src="{{ $page->image_thumbnail_url }}"
class="img-thumbnail"
style="max-width:220px">

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

                        Biblioteca de Mídia

                    </button>

                    <input
                        type="hidden"
                        name="media_id"
                        id="media_id"
                        value="{{ old('media_id',$page->media_id) }}">

                </div>

                <div class="mb-3">

                    <label class="form-label">

                       <b>Conteúdo</b>

                    </label>

                    <textarea
                        name="description"
                        rows="12"
                        class="form-control">{{ old('description',$page->description) }}</textarea>

                </div>

                <div class="form-check mb-4">

                    <input
                        type="checkbox"
                        name="active"
                        value="1"
                        class="form-check-input"
                        {{ $page->active ? 'checked' : '' }}>

                    <label class="form-check-label">

                        <b>Publicar página</b>

                    </label>

                </div>

                <button class="btn btn-primary">

                    <i class="bi bi-check-circle"></i>

                    Salvar Alterações

                </button>

            </div>

        </div>

    </form>

</div>

@include('admin.media.modal')

<script>

function previewLocalImage(event){

    document.getElementById('media_id').value='';

    const file = event.target.files[0];

    if(!file){
        return;
    }

    const preview = document.getElementById('media_preview');

    preview.innerHTML = '';

    const img = document.createElement('img');

    img.src = URL.createObjectURL(file);

    img.className = 'img-thumbnail';

    img.style.maxWidth = '220px';

    preview.appendChild(img);

}

</script>

@endsection