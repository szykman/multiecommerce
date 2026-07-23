@extends('admin.layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>
            <i class="bi bi-images"></i>
            Biblioteca de Mídia
        </h2>

        <a href="{{ route('media.create') }}"
           class="btn btn-primary">

            <i class="bi bi-cloud-upload"></i>

            Enviar Arquivos

        </a>

    </div>


@if(session('success'))

<div class="alert alert-success">

{{ session('success') }}

</div>

@endif


@if($media->count())



<div class="card shadow-sm mb-4">

<div class="card-body">

<div class="row g-3">


<div class="col-md-4">

<input
id="media_search"
type="text"
class="form-control"
placeholder="Pesquisar:">

</div>


<div class="col-md-3">

<select
id="filter_type"
class="form-select">

<option value="">Todos os Tipos</option>

<option value="image">Imagens</option>

<option value="video">Vídeos</option>

<option value="audio">Áudios</option>

<option value="application">Documentos</option>

</select>

</div>


<div class="col-md-3">

<select
id="filter_folder"
class="form-select">

<option value="">Todas as Pastas</option>

@foreach($media->pluck('folder')->unique() as $folder)

<option value="{{ strtolower($folder) }}">

{{ $folder }}

</option>

@endforeach

</select>

</div>


<div class="col-md-2 text-end">

<span class="badge bg-secondary fs-6">

{{ $media->total() }}

arquivo(s)

</span>

</div>


</div>

</div>

</div>



<div class="row">


@foreach($media as $item)


<div
class="col-xl-2 col-lg-3 col-md-4 col-6 mb-4 media-col">


<div
class="card h-100 shadow-sm media-item"

data-name="{{ strtolower($item->name) }}"
data-type="{{ strtolower($item->type) }}"
data-folder="{{ strtolower($item->folder) }}"
data-extension="{{ strtolower($item->extension) }}">


@if(Str::startsWith($item->mime,'image'))

<img
src="{{ asset('storage/'.$item->file) }}"
class="card-img-top"
style="height:160px;object-fit:cover;">

@elseif(Str::startsWith($item->mime,'video'))

<div class="text-center py-5">

<i class="bi bi-play-circle display-4"></i>

</div>

@elseif(Str::startsWith($item->mime,'audio'))

<div class="text-center py-5">

<i class="bi bi-music-note-beamed display-4"></i>

</div>

@elseif($item->extension=='pdf')

<div class="text-center py-5">

<i class="bi bi-file-earmark-pdf display-4 text-danger"></i>

</div>

@else

<div class="text-center py-5">

<i class="bi bi-file-earmark display-4"></i>

</div>

@endif


<div class="card-body">


<h6 class="text-truncate mb-2">

{{ $item->name }}

</h6>


<div class="small text-muted">

<strong>Tamanho:</strong>

{{ number_format($item->size/1024,1) }} KB

</div>


<div class="small text-muted">

<strong>Tipo:</strong>

{{ strtoupper($item->extension) }}

</div>


<div class="small text-muted">

<strong>Pasta:</strong>

{{ $item->folder }}

</div>


<div class="small text-muted mb-3">

{{ $item->created_at->format('d/m/Y H:i') }}

</div>


<form
method="POST"
action="{{ route('media.destroy',$item) }}"
onsubmit="return confirm('Excluir este arquivo da biblioteca?')">

@csrf
@method('DELETE')

<button
type="submit"
class="btn btn-danger btn-sm w-100">

<i class="bi bi-trash"></i>

Excluir

</button>

</form>


</div>

</div>

</div>

@endforeach


</div>


<div class="mt-4">

{{ $media->links() }}

</div>


@else


<div class="alert alert-info">

Nenhum arquivo enviado.

</div>


@endif


</div>



<script>

function filterMedia(){

    let text=document
        .getElementById('media_search')
        .value
        .toLowerCase();

    let type=document
        .getElementById('filter_type')
        .value
        .toLowerCase();

    let folder=document
        .getElementById('filter_folder')
        .value
        .toLowerCase();


    document.querySelectorAll('.media-col').forEach(function(col){

        let card=col.querySelector('.media-item');

        let search=
            card.dataset.name+" "+
            card.dataset.type+" "+
            card.dataset.folder+" "+
            card.dataset.extension;

        let ok=true;

        if(text && !search.includes(text))
            ok=false;

        if(type && card.dataset.type!=type)
            ok=false;

        if(folder && card.dataset.folder!=folder)
            ok=false;

        col.style.display=ok ? '' : 'none';

    });

}


document
.getElementById('media_search')
.addEventListener('keyup',filterMedia);


document
.getElementById('filter_type')
.addEventListener('change',filterMedia);


document
.getElementById('filter_folder')
.addEventListener('change',filterMedia);

</script>

@endsection
