
<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">

<div class="container">

<a class="navbar-brand d-flex align-items-center gap-2" href="/">

@if(isset($store->logo))

<img
src="{{ asset('storage/'.$store->logo) }}"
height="42"
class="rounded">

@endif

<span class="fw-bold">

{{ $store->name }}

</span>

</a>


<button class="navbar-toggler"
type="button"
data-bs-toggle="collapse"
data-bs-target="#mainMenu">

<span class="navbar-toggler-icon"></span>

</button>


<div class="collapse navbar-collapse" id="mainMenu">


<form class="d-flex mx-auto w-50"
method="GET"
action="/">

<input
class="form-control"
type="search"
name="search"
placeholder="Buscar produtos..."
value="{{ request('search') }}">

<button class="btn btn-primary ms-2">

<i class="bi bi-search"></i>

</button>

</form>



<ul class="navbar-nav ms-auto align-items-center">


<li class="nav-item">

<a class="nav-link" href="#">

<i class="bi bi-person"></i>

Login

</a>

</li>


<li class="nav-item">

<a class="nav-link" href="#">

<i class="bi bi-cart"></i>

Carrinho

</a>

</li>


</ul>


</div>

</div>

</nav>
