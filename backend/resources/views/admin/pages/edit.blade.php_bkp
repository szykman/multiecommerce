
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

Categoria CMS

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

Título

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

Imagem de Capa

</label>

@if($page->image)

<div class="mb-2">

<img
src="{{ asset('storage/'.$page->image) }}"
style="max-width:220px"
class="img-thumbnail">

</div>

@endif

<input
type="file"
name="image"
class="form-control">

</div>

<div class="mb-3">

<label class="form-label">

Conteúdo

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

Publicar página

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

@endsection
