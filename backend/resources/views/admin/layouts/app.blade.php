<!DOCTYPE html>
<html lang="pt-BR">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>MultiEcommerce Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">

<div class="container-fluid">

<a class="navbar-brand" href="/admin">
MultiEcommerce
</a>

<div class="text-white">

{{ auth()->user()->name }}

<form method="POST"
      action="{{ route('admin.logout') }}"
      class="d-inline">

@csrf

<button class="btn btn-sm btn-danger">
Sair
</button>

</form>

</div>

</div>

</nav>

<div class="container-fluid">

<div class="row">

<div class="col-md-2 bg-white border-end vh-100 p-3">

<div class="list-group">

<a href="/admin"
class="list-group-item">
Dashboard
</a>

<a href="/admin/products"
class="list-group-item">
Produtos
</a>

<a href="/admin/categories"
class="list-group-item">
Categorias
</a>

</div>

</div>

<div class="col-md-10 p-4">

@yield('content')

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
