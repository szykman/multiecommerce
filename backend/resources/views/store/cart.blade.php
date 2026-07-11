@extends('store.layout')

@section('content')

<div class="container py-5">

<h1>

Carrinho

</h1>

@if(count($cart))

<table class="table">

<tr>

<th>Produto</th>

<th>Qtd</th>

<th>Preço</th>

<th></th>

</tr>

@foreach($cart as $item)

<tr>

<td>

{{ $item['name'] }}

</td>

<td>

{{ $item['qty'] }}

</td>

<td>

R$ {{ number_format($item['price'],2,',','.') }}

</td>

<td>

<form
method="POST"
action="{{ route('store.cart.remove',$item['id']) }}">

@csrf

<button
class="btn btn-danger btn-sm">

Remover

</button>

</form>

</td>

</tr>

@endforeach

</table>

@else

<p>

Carrinho vazio.

</p>

@endif

</div>

@endsection
