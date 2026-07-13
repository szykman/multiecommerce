@extends('admin.layouts.app')

@section('content')

<div class="container">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>Nova Página</h2>

<a
href="{{ route('pages.index') }}"
class="btn btn-secondary">

Voltar

</a>

</div>

<form
method="POST"
action="{{ route('pages.store') }}"
enctype="multipart/form-data">

@csrf

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

<option value="{{ $category->id }}">

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
required>

</div>

<div class="mb-3">

<label class="form-label">

Imagem de Capa

</label>

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
class="form-control"></textarea>

</div>

<div class="form-check mb-4">

<input
type="checkbox"
name="active"
value="1"
checked
class="form-check-input">

<label class="form-check-label">

Publicar página

</label>

</div>

<button class="btn btn-primary">

Salvar Página

</button>

</div>

</div>

</form>

</div>

@endsection
