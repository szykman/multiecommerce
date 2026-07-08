<!doctype html>

<html lang="pt-br">

<head>

<meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>MultiEcommerce</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<nav class="navbar navbar-dark bg-dark">

<div class="container">

<a class="navbar-brand" href="/">

MultiEcommerce

</a>

</div>

</nav>

<div class="container mt-5">

<h2>Nossos Produtos</h2>

<div class="row">

@foreach($products as $product)

<div class="col-md-3">

<div class="card mb-4">

@if($product->image)

<img
src="{{ asset('storage/'.$product->image) }}"
class="card-img-top"
style="height:220px;object-fit:cover;">

@endif

<div class="card-body">

<h5>

{{ $product->name }}

</h5>

<p>

R$ {{ number_format($product->price,2,',','.') }}

</p>

<a
href="#"
class="btn btn-success w-100">

Comprar

</a>

</div>

</div>

</div>

@endforeach

</div>

</div>

</body>

</html>
