<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm store-navbar">

<div class="container">


<a href="/" class="navbar-brand d-flex align-items-center">


@if($settings->logo)

<img
src="{{ asset('storage/'.$settings->logo) }}"
height="42"
class="me-2 animate">

@endif


<strong>

{{ $store->name }}

</strong>


</a>




<button
class="navbar-toggler"
type="button"
data-bs-toggle="collapse"
data-bs-target="#mainMenu">

<span class="navbar-toggler-icon"></span>

</button>




<div
class="collapse navbar-collapse"
id="mainMenu">



<ul class="navbar-nav ms-4">



@foreach($cmsCategories as $category)


@if($category->products->count() == 1)


<li class="nav-item animate">


<a 
class="nav-link"
href="{{ route('store.page',$category->products->first()->slug) }}">

{{ $category->name }}

</a>


</li>



@elseif($category->products->count() > 1)



<li class="nav-item dropdown animate">


<a 
class="nav-link dropdown-toggle"
href="#"
role="button"
data-bs-toggle="dropdown">

{{ $category->name }}

</a>



<ul class="dropdown-menu">


@foreach($category->products as $page)


<li>


<a 
class="dropdown-item"
href="{{ route('store.page',$page->slug) }}">

{{ $page->name }}

</a>


</li>


@endforeach


</ul>


</li>



@endif


@endforeach



</ul>





<form
class="d-flex ms-auto me-3"
method="GET"
action="/">

<input
class="form-control"
type="search"
name="search"
placeholder="Buscar produtos..."
value="{{ request('search') }}">


<button 
class="btn btn-primary ms-2">

<i class="bi bi-search"></i>

</button>


</form>





<ul class="navbar-nav align-items-center">



<li class="nav-item">

<a
class="nav-link"
href="#">

<i class="bi bi-person"></i>

Login

</a>

</li>



<li class="nav-item">


<a
href="{{ route('store.cart') }}"
class="btn btn-outline-primary ms-2 animate">


<i class="bi bi-cart3"></i>


</a>


</li>




<li class="nav-item">


<button
id="theme-toggle"
class="btn btn-outline-secondary ms-2 animate">


<i id="theme-icon" class="bi bi-moon-stars"></i> 
</button>


</li>



</ul>


</div>

</div>

</nav>
