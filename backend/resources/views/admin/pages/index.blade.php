@extends('admin.layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Páginas CMS</h2>

        <a href="{{ route('pages.create') }}" class="btn btn-primary">
            <i class="bi bi-file-earmark-plus"></i>
            Nova Página
        </a>

    </div>

    @forelse($categories as $category)

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-dark text-white">

                <strong>{{ $category->name }}</strong>

                <span class="badge bg-secondary float-end">
                    {{ $category->products->count() }}
                </span>

            </div>

            <div class="card-body p-0">

                @if($category->products->count())

                <table class="table table-hover mb-0">

                    <thead>

                        <tr>

                            <th width="60">ID</th>

                            <th>Título</th>

                            <th width="180">Slug</th>

                            <th width="170">Status</th>

                            <th width="180" class="text-end">Ações</th>

                        </tr>

                    </thead>

                    <tbody>

                    @foreach($category->products as $page)

                        <tr>

                            <td>{{ $page->id }}</td>

                            <td>{{ $page->name }}</td>

                            <td>{{ $page->slug }}</td>

                            <td>

                                @if($page->active)

                                    <span class="badge bg-success">
                                        Publicada
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Rascunho
                                    </span>

                                @endif

                            </td>

                            <td class="text-end">

                                <a
                                    href="{{ route('pages.edit',$page) }}"
                                    class="btn btn-sm btn-outline-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>
<form
action="{{ route('pages.destroy',$page) }}"
method="POST"
class="d-inline"
onsubmit="return confirm('Excluir esta página?')">

@csrf
@method('DELETE')

<button class="btn btn-sm btn-danger">

<i class="bi bi-trash"></i>

</button>

</form>


                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

                @else

                    <div class="p-4 text-muted">

                        Nenhuma página cadastrada nesta categoria.

                    </div>

                @endif

            </div>

        </div>

    @empty

        <div class="alert alert-info">

            Nenhuma categoria CMS encontrada.

        </div>

    @endforelse

</div>

@endsection
