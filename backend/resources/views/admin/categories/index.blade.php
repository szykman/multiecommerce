@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Categorias</h2>

    <a href="{{ route('categories.create') }}"
       class="btn btn-primary">

        + Nova Categoria

    </a>

</div>

<table class="table table-striped table-hover align-middle">

    <thead class="table-dark">

        <tr>

            <th>ID</th>

<th>

<a class="text-white text-decoration-none"
href="?sort=name&direction={{ request('direction')=='asc'?'desc':'asc' }}">

Categoria

@if(request('sort')=='name')

{{ request('direction')=='asc' ? '▲' : '▼' }}

@endif

</a>

</th>

<th>

<a class="text-white text-decoration-none"
href="?sort=slug&direction={{ request('direction')=='asc'?'desc':'asc' }}">

Slug

@if(request('sort')=='slug')

{{ request('direction')=='asc' ? '▲' : '▼' }}

@endif

</a>

</th>


<th>

<a class="text-white text-decoration-none"
href="?sort=products_count&direction={{ request('direction')=='asc'?'desc':'asc' }}">

Produtos

@if(request('sort')=='products_count')

{{ request('direction')=='asc' ? '▲' : '▼' }}

@endif

</a>

</th>


<th>



<a class="text-white text-decoration-none"
href="?sort=products_count&direction={{ request('direction')=='asc'?'desc':'asc' }}">

Tipo

@if(request('sort')=='type')

{{ request('direction')=='asc' ? '▲' : '▼' }}

@endif

</a>




</th>


<th>

<a class="text-white text-decoration-none"
href="?sort=active&direction={{ request('direction')=='asc'?'desc':'asc' }}">

Status

@if(request('sort')=='active')

{{ request('direction')=='asc' ? '▲' : '▼' }}

@endif

</a>

</th>


            <th width="180">

                Ações

            </th>

        </tr>

    </thead>

    <tbody>

    @forelse($categories as $category)

        <tr>

            <td>

                {{ $category->id }}

            </td>

            <td>

                <strong>

                    {{ $category->name }}

                </strong>

            </td>

            <td>

                {{ $category->slug }}

            </td>

<td>

    {{ $category->products_count }}

</td>     


<td>

@if($category->type == 'store')

<span class="badge bg-primary">

<i class="bi bi-shop"></i>

Loja

</span>

@else

<span class="badge bg-success">

<i class="bi bi-file-earmark-richtext"></i>

CMS

</span>

@endif

</td>


       <td>



                @if($category->active)

                    <span class="badge bg-success">

                        Ativa

                    </span>

                @else

                    <span class="badge bg-danger">

                        Inativa

                    </span>

                @endif

            </td>

  <td>

                                <a
                                    href="{{ route('categories.edit',$category) }}"
                                    class="btn btn-sm btn-outline-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>
<form
action="{{ route('categories.destroy',$category) }}"
method="POST"
class="d-inline"
onsubmit="return confirm('Deseja realmente excluir esta categoria?')">

@csrf
@method('DELETE')

<button class="btn btn-sm btn-danger">

<i class="bi bi-trash"></i>

</button>

</form>


                            </td>



        </tr>

    @empty

        <tr>

            <td colspan="5" class="text-center">

                Nenhuma categoria cadastrada.

            </td>

        </tr>

    @endforelse

    </tbody>

</table>

@endsection
