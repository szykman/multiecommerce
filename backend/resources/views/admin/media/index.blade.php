@extends('admin.layouts.app')

@section('content')

<div class="container">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>
<i class="bi bi-images"></i>
Biblioteca de Mídia
</h2>

<a
href="{{ route('media.create') }}"
class="btn btn-primary">

<i class="bi bi-cloud-upload"></i>

Enviar Arquivos

</a>

</div>





@if($media->count())


<div class="row">


@foreach($media as $item)


<div class="col-xl-2 col-lg-3 col-md-4 col-6 mb-4">


<div class="card h-100 shadow-sm">


@if(Str::startsWith($item->mime,'image'))

<img
src="{{ asset('storage/'.$item->file) }}"
class="card-img-top"
style="height:150px;object-fit:cover;">


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


<strong class="d-block text-truncate">

{{ $item->name }}

</strong>


<div class="small text-muted">

{{ number_format($item->size/1024,1) }} KB

<h6>
{{ $item->name }}
</h6>

</div>


<div class="small">

Pasta:
{{ $item->folder }}

</div>


<div class="small text-muted">

{{ $item->created_at->format('d/m/Y H:i') }}

</div>


<div class="mt-3">


<form
method="POST"
action="{{ route('media.destroy',$item->id) }}"
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


</div>


@endforeach


</div>


{{ $media->links() }}


@else


<div class="alert alert-info">

Nenhum arquivo enviado.

</div>


@endif


</div>


@endsection
