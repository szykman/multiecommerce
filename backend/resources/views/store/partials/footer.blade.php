<!-- FOOTER -->

<footer class="store-footer text-white py-5 mt-5">

<div class="container">

<div class="row">

<div class="col-md-6">

<h5>

{{ $store->name }}

</h5>


@if($settings->document)

<div>

<strong>CNPJ:</strong>

{{ $settings->document }}

</div>

@endif


{{ $settings->footer_text ?? 'Powered by MultiHost' }}

@if($settings?->hours)

<p class="small text-light mb-0">

<strong>Horário:</strong><br>

{!! nl2br(e($settings->hours)) !!}

</p>

@endif

</div>

<div class="col-md-6 text-md-end">

@if($settings?->phone)

<div>

<i class="bi bi-telephone"></i>

{{ $settings->phone }}

</div>

@endif

@if($settings?->whatsapp)

<div>

<i class="bi bi-whatsapp"></i>

{{ $settings->whatsapp }}

</div>

@endif

@if($settings?->email)

<div>

<i class="bi bi-envelope"></i>

{{ $settings->email }}

</div>

@endif

<div class="mt-3">

@if($settings?->instagram)

<a
href="{{ $settings->instagram }}"
target="_blank"
class="text-white me-3">

<i class="bi bi-instagram fs-5"></i>

</a>

@endif

@if($settings?->facebook)

<a
href="{{ $settings->facebook }}"
target="_blank"
class="text-white me-3">

<i class="bi bi-facebook fs-5"></i>

</a>

@endif

@if($settings?->youtube)

<a
href="{{ $settings->youtube }}"
target="_blank"
class="text-white me-3">

<i class="bi bi-youtube fs-5"></i>

</a>

@endif

@if($settings?->tiktok)

<a
href="{{ $settings->tiktok }}"
target="_blank"
class="text-white">

<i class="bi bi-tiktok fs-5"></i>

</a>

@endif

</div>

@if($settings?->copyright)

<p class="small mt-4 mb-0">

{{ $settings->copyright }}

</p>

@else

<p class="small mt-4 mb-0">

© {{ date('Y') }} {{ $store->name }}

</p>

@endif

</div>

</div>

@if($settings?->google_maps)

<div class="row mt-4">

<div class="col-12">

{!! $settings->google_maps !!}

</div>

</div>

@endif

</div>

</footer>
