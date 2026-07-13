@extends('store.layout')

@section('content')

@include('store.partials.header')

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-10">

@if($page->image)

<img
src="{{ asset('storage/'.$page->image) }}"
class="img-fluid rounded shadow mb-4 w-100">

@endif

<h1 class="mb-4">

{{ $page->name }}

</h1>

<div class="fs-5">

{!! nl2br(e($page->description)) !!}

</div>

</div>

</div>

</div>

@include('store.partials.footer')

@endsection
