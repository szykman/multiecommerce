@extends('admin.layouts.app')

@section('content')

<div class="container">

<h2 class="mb-4">

<i class="bi bi-cloud-upload"></i>

Enviar Arquivos

</h2>


<div class="card">

<div class="card-body">


<div id="drop-area"
class="border border-2 rounded p-5 text-center">


<i class="bi bi-cloud-arrow-up fs-1"></i>


<h5 class="mt-3">

Arraste arquivos aqui

</h5>


<p class="text-muted">

ou clique para selecionar

</p>


<input
type="file"
id="file-input"
multiple
hidden>


<button
type="button"
id="select-files"
class="btn btn-primary">

Selecionar Arquivos

</button>


</div>



<div id="preview"
class="row mt-4">

</div>



<div class="mt-4">

<button
id="upload-btn"
class="btn btn-success"
disabled>

<i class="bi bi-upload"></i>

Enviar

</button>

</div>


<div id="progress"
class="mt-3">

</div>


</div>

</div>


</div>


<script src="{{ asset('js/admin/media-upload.js') }}"></script>


@endsection
