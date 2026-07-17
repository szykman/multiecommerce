<!doctype html>
<html lang="pt-br">

<head>

<meta charset="utf-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>
{{ $store->name }}
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/store/style.css') }}">

<style>

:root{

--primary: {{ $settings->primary_color ?? '#0d6efd' }};
--secondary: {{ $settings->secondary_color ?? '#6c757d' }};
--radius: {{ $settings->radius ?? 8 }}px;
--font: '{{ $settings->font ?? 'system-ui' }}';

}

body{

font-family:var(--font);
transition:background-color .3s,color .3s;

}


/* NAVBAR */

.store-navbar{

background:var(--primary)!important;

}


/* CORES */

.btn-primary{

background:var(--primary);
border-color:var(--primary);

}

.btn-secondary{

background:var(--secondary);
border-color:var(--secondary);

}


/* RADIUS */

.btn,
.card,
.form-control,
.form-select{

border-radius:var(--radius);

}


/* SOMBRAS */

@if(($settings->shadow ?? 'medium') == 'none')

.card,
.btn,
.form-control{

box-shadow:none!important;

}

@endif

@if(($settings->shadow ?? 'medium') == 'small')

.card{

box-shadow:0 .125rem .25rem rgba(0,0,0,.075);

}

@endif

@if(($settings->shadow ?? 'medium') == 'large')

.card{

box-shadow:0 1rem 3rem rgba(0,0,0,.175);

}

@endif


/* TEMA ESCURO */

body.dark{

background:#121212!important;
color:#eee;

}

body.dark .card{

background:#1e1e1e;
color:#eee;

}

body.dark .navbar{

background:#000!important;

}

body.dark .dropdown-menu{

background:#222;
border-color:#333;

}

body.dark .dropdown-item{

color:#eee;

}

body.dark .dropdown-item:hover{

background:#333;

}

body.dark .form-control{

background:#2b2b2b;
color:white;
border-color:#444;

}

body.dark .form-control::placeholder{

color:#bbb;

}

.store-footer{

background:var(--primary);
color:#fff;

}

body.dark .store-footer{

background:#000;
color:#fff;

}

/* FUNDOS BOOTSTRAP */

body.dark .bg-white{

background:#181818 !important;
color:#eee !important;

}

body.dark .bg-light{

background:#1d1d1d !important;
color:#eee !important;

}

body.dark .bg-dark{

background:#000 !important;
color:#eee !important;

}

body.dark footer{

background:#000 !important;
color:#eee !important;

}

body.dark section{

color:#eee;

}

body.dark{

background:#121212 !important;
color:#eee;

}

body.dark section{

background:#121212;
color:#eee;

}




/* ANIMAÇÕES */

@if($settings->animations ?? true)

.animate,
.card,
.btn{

transition:all .30s ease;

}

.card:hover{

transform:translateY(-5px);

}

@endif

.favorite-btn{

background:none;
border:none;
font-size:1.8rem;
color:#777;
cursor:pointer;

transition:.3s;

}


.favorite-btn:hover{

transform:scale(1.15);

}


.favorite-btn.active{

color:#dc3545;

}


body.dark .favorite-btn{

color:#aaa;

}


body.dark .favorite-btn.active{

color:#ff4d5a;

}


</style>

</head>

<body>

@yield('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>

const THEME_FROM_STORE = "{{ $settings->theme ?? 'auto' }}";

const button = document.getElementById('theme-toggle');

const icon = document.getElementById('theme-icon');


function applyTheme(theme){

    if(theme === 'dark'){

        document.body.classList.add('dark');

    }else{

        document.body.classList.remove('dark');

    }

    updateIcon();

}


function updateIcon(){

    if(!icon) return;

    if(document.body.classList.contains('dark')){

        icon.className='bi bi-sun';

    }else{

        icon.className='bi bi-moon-stars';

    }

}


// preferência do visitante

let savedTheme = localStorage.getItem('theme');


if(savedTheme){

    applyTheme(savedTheme);

}else{

    // padrão da loja

    if(THEME_FROM_STORE === 'dark'){

        applyTheme('dark');

    }else if(THEME_FROM_STORE === 'light'){

        applyTheme('light');

    }else{

        // automático

        if(window.matchMedia('(prefers-color-scheme: dark)').matches){

            applyTheme('dark');

        }else{

            applyTheme('light');

        }

    }

}


if(button){

    button.addEventListener('click',function(){

        const dark = document.body.classList.contains('dark');

        const newTheme = dark ? 'light' : 'dark';

        applyTheme(newTheme);

        localStorage.setItem('theme',newTheme);

    });

}


document.querySelectorAll('.favorite-btn')
.forEach(button=>{


button.addEventListener('click',function(){


this.classList.toggle('active');


let icon=this.querySelector('i');


if(this.classList.contains('active')){


icon.className='bi bi-heart-fill';


}else{


icon.className='bi bi-heart';


}


});


});



</script>

</body>

</html>
