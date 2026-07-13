@extends('admin.layouts.app')

@section('content')

<div class="container">

<h2 class="mb-4">

<i class="bi bi-cloud-upload"></i>

Enviar Arquivos

</h2>

<div class="card">

<div class="card-body">

<form
method="POST"
action="{{ route('media.store') }}"
enctype="multipart/form-data">

@csrf

<div class="mb-3">

<label>

Pasta

</label>

<select
class="form-select"
name="folder">

<option>Geral</option>

<option>Produtos</option>

<option>Categorias</option>

<option>CMS</option>

<option>Banner</option>

<option>Logo</option>

<option>Downloads</option>

</select>

</div>

<div class="mb-4">

<label>

Arquivos

</label>

<input
type="file"
class="form-control"
name="files[]"
multiple>

<div class="form-text">

Pode selecionar vários arquivos.

</div>

</div>

<button
class="btn btn-primary">

<i class="bi bi-cloud-upload"></i>

Enviar

</button>

<a
href="{{ route('media.index') }}"
class="btn btn-secondary">

Cancelar

</a>

</form>

</div>

</div>

</div>

@endsection
