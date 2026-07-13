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


@if(session('success'))

<div class="alert alert-success">

{{ session('success') }}

</div>

@endif


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

<strong>

{{ $item->name }}

</strong>

<div class="small text-muted">

{{ number_format($item->size/1024,1) }} KB

</div>

<div class="small">

{{ $item->folder }}

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
