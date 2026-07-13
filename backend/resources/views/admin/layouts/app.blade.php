<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>MultiEcommerce Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">

<div class="container-fluid">

<a class="navbar-brand fw-bold" href="/admin">

🛒 MultiEcommerce

</a>

<div class="d-flex align-items-center">

<span class="text-white me-3">

<b>{{ auth()->user()->name }}</b>

</span>

<form method="POST"
      action="{{ route('admin.logout') }}"
      class="m-0">

@csrf

<button class="btn btn-danger btn-sm">

<i class="bi bi-box-arrow-right"></i>

Sair

</button>

</form>

</div>

</div>

</nav>

<div class="container-fluid">

<div class="row">

<div class="col-md-2 bg-white border-end vh-100 p-3">

<h6 class="text-secondary mb-3">

MENU

</h6>

<div class="list-group shadow-sm">

<a href="/admin"
class="list-group-item list-group-item-action">

<i class="bi bi-speedometer2"></i>

Dashboard

</a>

<a href="/admin/products"
class="list-group-item list-group-item-action">

<i class="bi bi-box-seam"></i>

Produtos

</a>

<a href="/admin/categories"
class="list-group-item list-group-item-action">

<i class="bi bi-tags"></i>

Categorias

</a>

<a href="/admin/pages"
class="list-group-item list-group-item-action">

<i class="bi bi-file-earmark-text"></i>

Páginas

</a>


    <a
        href="{{ route('media.index') }}"
        class="list-group-item list-group-item-action">

        <i class="bi bi-images"></i>

        Biblioteca de Mídia

    </a>



<a href="#"
class="list-group-item list-group-item-action disabled">

<i class="bi bi-receipt"></i>

Pedidos

</a>

<a href="#"
class="list-group-item list-group-item-action disabled">

<i class="bi bi-people"></i>

Clientes

</a>


    <a class="list-group-item list-group-item-action" href="{{ route('settings.edit') }}">

<i class="bi bi-gear"></i>

Configurações

</a>

</div>

</div>

<div class="col-md-10 p-4">

@if(session('success'))

<div class="alert alert-success alert-dismissible fade show shadow-sm">

    <i class="bi bi-check-circle"></i>

    {{ session('success') }}

    <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
    </button>

</div>

@endif


@if(session('error'))

<div class="alert alert-danger alert-dismissible fade show shadow-sm">

    <i class="bi bi-exclamation-triangle"></i>

    {{ session('error') }}

    <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
    </button>

</div>

@endif


@if($errors->any())

<div class="alert alert-danger shadow-sm">

    <strong>Verifique os erros:</strong>

    <ul class="mb-0">

        @foreach($errors->all() as $error)

            <li>{{ $error }}</li>

        @endforeach

    </ul>

</div>

@endif


@yield('content')

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
