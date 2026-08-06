@extends('admin.layouts.app')

@include('admin.media.modal')

@section('content')

<div class="container">

<h2 class="mb-4">
    Configurações da Loja
</h2>

<form
method="POST"
action="{{ route('settings.update') }}"
enctype="multipart/form-data">

@csrf
@method('PUT')

<ul class="nav nav-tabs mb-4" role="tablist">

<li class="nav-item">
<a class="nav-link active"
data-bs-toggle="tab"
href="#geral">
<i class="bi bi-shop"></i>
Geral
</a>
</li>

<li class="nav-item">
<a class="nav-link"
data-bs-toggle="tab"
href="#aparencia">
<i class="bi bi-palette"></i>
Aparência
</a>
</li>

<li class="nav-item">
<a class="nav-link"
data-bs-toggle="tab"
href="#contato">
<i class="bi bi-telephone"></i>
Contato
</a>
</li>

<li class="nav-item">
<a class="nav-link"
data-bs-toggle="tab"
href="#social">
<i class="bi bi-share"></i>
Redes Sociais
</a>
</li>

<li class="nav-item">
<a class="nav-link"
data-bs-toggle="tab"
href="#seo">
<i class="bi bi-google"></i>
SEO
</a>
</li>

<li class="nav-item">

<a class="nav-link"
data-bs-toggle="tab"
href="#recursos">

<i class="bi bi-toggle-on"></i>

Recursos

</a>

</li>

<li class="nav-item">
<a class="nav-link"
data-bs-toggle="tab"
href="#footer">
<i class="bi bi-layout-text-window"></i>
Rodapé
</a>
</li>

</ul>

<div class="tab-content">

<div class="tab-pane fade show active" id="geral">

<div class="card mb-4">

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">
<label><strong>Nome da Empresa</label></strong>
<input
type="text"
class="form-control"
name="name"
value="{{ old('name',$store->name) }}">
</div>

<div class="col-md-6 mb-3">
<label><strong>E-mail</label></strong>
<input
type="email"
class="form-control"
name="email"
value="{{ old('email',$settings->email ?? '') }}">
</div>

<div class="col-md-6 mb-3">

<label><strong>CPF / CNPJ</label></strong>

<input
type="text"
class="form-control"
name="document"
placeholder="000.000.000-00 ou 00.000.000/0000-00"
value="{{ old('document',$settings->document ?? '') }}">

</div>



<div class="col-md-4 mb-3">

<label><strong><b>Logo</b></label></strong>

<div id="logo_preview">

@if($settings?->logo)

<img
src="{{ asset('storage/'.$settings->logo) }}"
class="img-thumbnail"
style="max-height:100px">

@endif

</div>

<input
type="file"
class="form-control mb-2"
name="logo"
onchange="previewSettingsImage(event,'logo_preview','logo_media_id')">

<button
type="button"
class="btn btn-outline-primary btn-sm"
onclick="openMediaPicker('logo')">

<i class="bi bi-images"></i>

Biblioteca de Mídia

</button>

<input
type="hidden"
name="logo_media_id"
id="logo_media_id">

</div>


<div class="col-md-4 mb-3">

<label><strong><b>Banner</b></label></strong>

<div id="banner_preview">

@if($settings?->banner)

<img
src="{{ asset('storage/'.$settings->banner) }}"
class="img-thumbnail w-100">

@endif

</div>

<input
type="file"
class="form-control mb-2"
name="banner"
onchange="previewSettingsImage(event,'banner_preview','banner_media_id')">

<button
type="button"
class="btn btn-outline-primary btn-sm"
onclick="openMediaPicker('banner')">

<i class="bi bi-images"></i>

Biblioteca de Mídia

</button>

<input
type="hidden"
name="banner_media_id"
id="banner_media_id">

</div>




<div class="col-md-4 mb-3">

<label><strong><b>Favicon</b></label></strong>

<div id="favicon_preview">

@if($settings?->favicon)

<img
src="{{ asset('storage/'.$settings->favicon) }}"
class="img-thumbnail"
style="max-height:48px">

@endif

</div>

<input
type="file"
class="form-control mb-2"
name="favicon"
onchange="previewSettingsImage(event,'favicon_preview','favicon_media_id')">

<button
type="button"
class="btn btn-outline-primary btn-sm"
onclick="openMediaPicker('favicon')">

<i class="bi bi-images"></i>

Biblioteca de Mídia

</button>

<input
type="hidden"
name="favicon_media_id"
id="favicon_media_id">

</div>

</div>

</div>

</div>

</div>

<div class="tab-pane fade" id="aparencia">

<div class="card">

<div class="card-body">

<div class="row">

<div class="col-md-4">

<label><strong>Cor Primária</label></strong>

<input
type="color"
class="form-control form-control-color"
name="primary_color"
value="{{ old('primary_color',$settings->primary_color ?? '#0d6efd') }}">

</div>

<div class="col-md-4">

<label><strong>Cor Secundária</label></strong>

<input
type="color"
class="form-control form-control-color"
name="secondary_color"
value="{{ old('secondary_color',$settings->secondary_color ?? '#6c757d') }}">

</div>

<div class="col-md-4">

<label><strong>Bordas</label></strong>

<select
class="form-select"
name="radius">

<option value="0" {{ old('radius',$settings->radius ?? 10)==0?'selected':'' }}>
Quadradas
</option>

<option value="10" {{ old('radius',$settings->radius ?? 10)==10?'selected':'' }}>
Levemente Arredondadas
</option>

<option value="20" {{ old('radius',$settings->radius ?? 10)==20?'selected':'' }}>
Arredondadas
</option>

<option value="50" {{ old('radius',$settings->radius ?? 10)==50?'selected':'' }}>
Muito Arredondadas
</option>

</select>

</div>


<div class="col-md-4 mt-3">

<label><strong>Tema</label></strong>

<select
class="form-select"
name="theme">

<option value="light"
{{ old('theme',$settings->theme ?? 'auto')=='light'?'selected':'' }}>

Claro

</option>

<option value="dark"
{{ old('theme',$settings->theme ?? 'auto')=='dark'?'selected':'' }}>

Escuro

</option>

<option value="auto"
{{ old('theme',$settings->theme ?? 'auto')=='auto'?'selected':'' }}>

Automático

</option>

</select>

</div>

<div class="col-md-4 mt-3">


<div class="col-md-4 mt-3">

<label>
<strong>Fonte da Loja</strong>
</label>

<select
class="form-select"
name="font"
id="font_selector"
style="width:300px;">

<option value="system-ui"
{{ old('font',$settings->font ?? 'system-ui')=='system-ui'?'selected':'' }}>
Padrão do Sistema
</option>


<option value="Arial"
{{ old('font',$settings->font ?? '')=='Arial'?'selected':'' }}>
Arial
</option>


<option value="Verdana"
{{ old('font',$settings->font ?? '')=='Verdana'?'selected':'' }}>
Verdana
</option>


<option value="Tahoma"
{{ old('font',$settings->font ?? '')=='Tahoma'?'selected':'' }}>
Tahoma
</option>


<option value="Trebuchet MS"
{{ old('font',$settings->font ?? '')=='Trebuchet MS'?'selected':'' }}>
Trebuchet MS
</option>


<option value="Georgia"
{{ old('font',$settings->font ?? '')=='Georgia'?'selected':'' }}>
Georgia
</option>


<option value="Times New Roman"
{{ old('font',$settings->font ?? '')=='Times New Roman'?'selected':'' }}>
Times New Roman
</option>


<option value="Courier New"
{{ old('font',$settings->font ?? '')=='Courier New'?'selected':'' }}>
Courier New
</option>


<option value="Impact"
{{ old('font',$settings->font ?? '')=='Impact'?'selected':'' }}>
Impact
</option>


<option value="Comic Sans MS"
{{ old('font',$settings->font ?? '')=='Comic Sans MS'?'selected':'' }}>
Comic Sans
</option>


<option value="Lucida Console"
{{ old('font',$settings->font ?? '')=='Lucida Console'?'selected':'' }}>
Lucida Console
</option>


<option value="Palatino Linotype"
{{ old('font',$settings->font ?? '')=='Palatino Linotype'?'selected':'' }}>
Palatino
</option>


</select>





</div>


<div 
id="font_preview"
class="border rounded mt-3 p-3 text-center"
style="font-size:22px">

MultiEcommerce

</div>


</div>





<div class="col-md-4 mt-3">

<label><strong>Sombras</label></strong>

<select
class="form-select"
name="shadow">

<option value="none"
{{ old('shadow',$settings->shadow ?? 'medium')=='none'?'selected':'' }}>

Sem sombra

</option>

<option value="small"
{{ old('shadow',$settings->shadow ?? 'medium')=='small'?'selected':'' }}>

Pequena

</option>

<option value="medium"
{{ old('shadow',$settings->shadow ?? 'medium')=='medium'?'selected':'' }}>

Média

</option>

<option value="large"
{{ old('shadow',$settings->shadow ?? 'medium')=='large'?'selected':'' }}>

Grande

</option>

</select>

</div>



<div class="col-md-12 mt-3">

<div class="form-check form-switch">

<input
class="form-check-input"
type="checkbox"
name="animations"
value="1"
{{ old('animations',$settings->animations ?? true)?'checked':'' }}>

<label class="form-check-label">

Habilitar animações e efeitos

</label></strong>

</div>

</div>


</div>

</div>

</div>

</div>

<div class="tab-pane fade" id="contato">

<div class="card">

<div class="card-body">

<div class="row">

<div class="col-md-6">

<label><strong>WhatsApp</label></strong>

<input
class="form-control"
name="whatsapp"
value="{{ old('whatsapp',$settings->whatsapp ?? '') }}">
</div>

<div class="col-md-6">

<label><strong>Telefone</label></strong>

<input
class="form-control"
name="phone"
value="{{ old('phone',$settings->phone ?? '') }}">

</div>

<div class="col-md-12 mt-3">

<label><strong>Horário</label></strong>

<textarea
class="form-control"
rows="3"
name="hours">{{ old('hours',$settings->hours ?? '') }}</textarea>

</div>

</div>

</div>

</div>

</div>

<div class="tab-pane fade" id="social">

<div class="card">

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">

<input
class="form-control"
name="instagram"
placeholder="Instagram"
value="{{ old('instagram',$settings->instagram ?? '') }}">

</div>

<div class="col-md-6 mb-3">
<input class="form-control" name="facebook" placeholder="Facebook"
value="{{ old('facebook',$settings->facebook ?? '') }}">
</div>

<div class="col-md-6 mb-3">
<input class="form-control" name="tiktok" placeholder="TikTok"
value="{{ old('tiktok',$settings->tiktok ?? '') }}">
</div>

<div class="col-md-6 mb-3">

<input class="form-control" name="youtube" placeholder="YouTube"
value="{{ old('youtube',$settings->youtube ?? '') }}">

</div>

</div>

</div>

</div>

</div>




<div class="tab-pane fade" id="recursos">

<div class="card">

<div class="card-body">

<h5 class="mb-4">
Recursos da Loja
</h5>


<div class="row">


@php

$features = [

'show_stock'=>'Mostrar estoque',

'show_favorites'=>'Mostrar favoritos',

'show_rating'=>'Mostrar avaliações',

'show_sale_price'=>'Mostrar preço promocional',

'show_related_products'=>'Mostrar produtos relacionados',

'show_sold_products'=>'Mostrar produtos vendidos',

'show_whatsapp_button'=>'Mostrar botão de chamar pelo WhatsApp',

'show_share'=>'Mostrar botão de compartilhamento',

'show_breadcrumbs'=>'Mostrar breadcrumbs',

];

@endphp



@foreach($features as $key=>$label)

<div class="col-md-6 mt-3">

<div class="form-check form-switch">


<input
type="hidden"
name="settings[resources][{{ $key }}]"
value="0">


<input 
class="form-check-input"
type="checkbox"
name="settings[resources][{{ $key }}]"
value="1"

{{ 
data_get(
$settings->settings,
"resources.$key",
true
)
?'checked':''
}}

>


<label class="form-check-label">

{{ $label }}

</label>


</div>

</div>


@endforeach


</div>


</div>

</div>

</div>





<div class="tab-pane fade" id="seo">

<div class="card">

<div class="card-body">

<label><strong>Título SEO</label></strong>

<input
class="form-control mb-3"
name="seo_title"
value="{{ old('seo_title',$settings->seo_title ?? '') }}">
<label><strong>Descrição</label></strong>

<textarea
rows="4"
class="form-control mb-3"
name="seo_description">{{ old('seo_description',$settings->seo_description ?? '') }}</textarea>


<label><strong>Palavras-chave</label></strong>

<textarea
rows="3"
class="form-control"
name="seo_keywords">{{ old('seo_keywords',$settings->seo_keywords ?? '') }}</textarea>

</div>

</div>

</div>

<div class="tab-pane fade" id="footer">

<div class="card">

<div class="card-body">

<label><strong>Google Maps</label></strong>

<textarea
rows="4"
class="form-control mb-3"
name="google_maps">{{ old('google_maps',$settings->google_maps ?? '') }}</textarea>


<label><strong>Copyright</label></strong>

<input
class="form-control mb-3"
name="copyright"
value="{{ old('copyright',$settings->copyright ?? '') }}">

<label><strong>Texto Rodapé</label></strong>

<textarea
rows="5"
class="form-control"
name="footer_text">{{ old('footer_text',$settings->footer_text ?? '') }}</textarea>


</div>

</div>

</div>

</div>

<div class="mt-4">

<button
class="btn btn-primary btn-lg">

<i class="bi bi-check-circle"></i>

Salvar Configurações

</button>

</div>

</form>

</div>

<script>

function previewSettingsImage(event,previewId,hiddenId){

    document.getElementById(hiddenId).value='';

    const file=event.target.files[0];

    if(!file){
        return;
    }

    const preview=document.getElementById(previewId);

    preview.innerHTML='';

    const img=document.createElement('img');

    img.src=URL.createObjectURL(file);

    img.className='img-thumbnail';

    img.style.maxHeight='100px';

    preview.appendChild(img);

}

</script>

<script>

const fontSelector = document.getElementById('font_selector');

const fontPreview = document.getElementById('font_preview');


function updateFontPreview(){

    let font = fontSelector.value;

    fontPreview.style.fontFamily = font;

}


if(fontSelector){

    updateFontPreview();


    fontSelector.addEventListener(
        'change',
        updateFontPreview
    );

}


</script>



<script>

document.addEventListener('DOMContentLoaded', function(){


    // recupera última aba aberta

    let activeTab = localStorage.getItem('settings_active_tab');


    if(activeTab){

        let tab = document.querySelector(
            'a[href="'+activeTab+'"]'
        );


        if(tab){

            let bootstrapTab = new bootstrap.Tab(tab);

            bootstrapTab.show();

        }

    }



    // grava quando trocar de aba

    document
    .querySelectorAll('[data-bs-toggle="tab"]')
    .forEach(function(tab){


        tab.addEventListener(
            'shown.bs.tab',
            function(event){


                let href = event.target.getAttribute('href');


                localStorage.setItem(
                    'settings_active_tab',
                    href
                );


            }
        );


    });



});

</script>

@endsection
