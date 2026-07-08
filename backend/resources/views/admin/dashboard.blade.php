@extends('admin.layouts.app')

@section('content')

<h2>Dashboard</h2>

<div class="row mt-4">

<div class="col-md-4">

<div class="card">

<div class="card-body">

<h5>Produtos</h5>

<h2>{{ \App\Models\Product::count() }}</h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card">

<div class="card-body">

<h5>Categorias</h5>

<h2>{{ \App\Models\Category::count() }}</h2>

</div>

</div>

</div>

</div>

@endsection
