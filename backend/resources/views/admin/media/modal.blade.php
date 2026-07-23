<div class="modal fade" id="mediaModal" tabindex="-1">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content">


            <div class="modal-header">

                <h5 class="modal-title">

                    <i class="bi bi-images"></i>

                    Biblioteca de Mídia

                </h5>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>


            </div>



            <div class="modal-body">


                <div class="row mb-3">


                    <div class="col-md-8">

                        <input
                            id="media_search"
                            type="text"
                            class="form-control"
                            placeholder="Pesquisar imagem...">

                    </div>



                    <div class="col-md-4 text-end">

                        <button
                            id="btnUseMedia"
                            type="button"
                            class="btn btn-primary"
                            disabled>

                            <i class="bi bi-check-circle"></i>

                            Usar imagem

                        </button>


                    </div>


                </div>



                <div class="row g-3" id="mediaGrid">


                    @foreach($media as $item)


                    <div class="col-xl-2 col-lg-3 col-md-4 col-6 media-col">


                        <div
                            class="card media-item h-100"

                            data-id="{{ $item->id }}"

                            data-name="{{ strtolower($item->name) }}"

                            data-image="{{ asset('storage/'.$item->file) }}"

                            style="cursor:pointer">


                            <img
                                src="{{ asset('storage/'.$item->file) }}"
                                class="card-img-top"
                                style="height:140px;object-fit:cover">



                            <div class="card-body p-2 text-center">

                                <small>

                                    {{ \Illuminate\Support\Str::limit($item->name,20) }}

                                </small>

                            </div>


                        </div>


                    </div>


                    @endforeach


                </div>



            </div>




            <div class="modal-footer">


                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Cancelar

                </button>


            </div>



        </div>

    </div>

</div>



<script>


let selectedMedia = null;

let currentField = null;



function openMediaPicker(field){


    currentField = field;


    if(field === 'gallery'){

        selectedMedia = [];

    }else{

        selectedMedia = null;

    }



    document
    .getElementById('btnUseMedia')
    .disabled = true;



    document
    .querySelectorAll('.media-item')
    .forEach(function(card){


        card.classList.remove(

            'border-primary',
            'border-3',
            'shadow'

        );


    });



    new bootstrap.Modal(
        document.getElementById('mediaModal')
    ).show();


}





document
.querySelectorAll('.media-item')
.forEach(function(card){



    card.addEventListener(
    'click',
    function(){



        /*
        |--------------------------------------------------------------------------
        | GALERIA MULTIPLA
        |--------------------------------------------------------------------------
        */


        if(currentField === 'gallery'){


            this.classList.toggle(
                'border-primary'
            );


            this.classList.toggle(
                'border-3'
            );



            let id = this.dataset.id;



            let index =
            selectedMedia.indexOf(id);



            if(index >= 0){


                selectedMedia.splice(
                    index,
                    1
                );


            }else{


                selectedMedia.push(id);


            }



            document
            .getElementById('btnUseMedia')
            .disabled =
            selectedMedia.length === 0;



            return;


        }




        /*
        |--------------------------------------------------------------------------
        | IMAGEM UNICA
        |--------------------------------------------------------------------------
        */


        document
        .querySelectorAll('.media-item')
        .forEach(function(c){


            c.classList.remove(

                'border-primary',
                'border-3',
                'shadow'

            );


        });



        this.classList.add(

            'border-primary',
            'border-3',
            'shadow'

        );



        selectedMedia = this;



        document
        .getElementById('btnUseMedia')
        .disabled = false;



    });


});






document
.getElementById('media_search')
.addEventListener(
'keyup',
function(){



    let txt =
    this.value.toLowerCase();



    document
    .querySelectorAll('.media-col')
    .forEach(function(col){


        let card =
        col.querySelector('.media-item');



        col.style.display =

        card.dataset.name.indexOf(txt)>=0

        ? ''

        : 'none';



    });


});






document
.getElementById('btnUseMedia')
.addEventListener(
'click',
function(){


    if(!selectedMedia){

        return;

    }



    /*
    |--------------------------------------------------------------------------
    | GALERIA MULTIPLA
    |--------------------------------------------------------------------------
    */


    if(currentField === 'gallery'){


        let html = '';

        let preview = '';



        selectedMedia.forEach(function(id){


            let card = document.querySelector(
                '.media-item[data-id="'+id+'"]'
            );


            if(!card){

                return;

            }



            let image = card.dataset.image;



            html += `

                <input
                type="hidden"
                name="gallery[]"
                value="${id}">

            `;



            preview += `

                <div class="col-md-3">

                    <div class="card shadow-sm">

                        <img
                        src="${image}"
                        class="card-img-top"
                        style="
                        height:180px;
                        object-fit:cover;
                        ">

                    </div>

                </div>

            `;


        });



        document
        .getElementById('gallery_inputs')
        .innerHTML = html;



        document
        .getElementById('gallery_upload_preview')
        .innerHTML = preview;



        bootstrap.Modal
        .getInstance(
            document.getElementById('mediaModal')
        )
        .hide();



        return;


    }





    /*
    |--------------------------------------------------------------------------
    | IMAGEM ÚNICA
    |--------------------------------------------------------------------------
    */


    let id =
    selectedMedia.dataset.id;



    let image =
    selectedMedia.dataset.image;




    switch(currentField){


        case 'logo':

            document
            .getElementById('logo_media_id')
            .value=id;


            document
            .getElementById('logo_preview')
            .innerHTML =
            '<img src="'+image+'" class="img-thumbnail" style="max-height:80px">';


        break;




        case 'banner':

            document
            .getElementById('banner_media_id')
            .value=id;


            document
            .getElementById('banner_preview')
            .innerHTML =
            '<img src="'+image+'" class="img-thumbnail w-100">';


        break;




        case 'favicon':

            document
            .getElementById('favicon_media_id')
            .value=id;


            document
            .getElementById('favicon_preview')
            .innerHTML =
            '<img src="'+image+'" class="img-thumbnail" style="max-height:48px">';


        break;




        default:


            if(document.getElementById('media_id')){


                document
                .getElementById('media_id')
                .value=id;


            }



            if(document.getElementById('media_preview')){


                document
                .getElementById('media_preview')
                .innerHTML =

                '<img src="'+image+'" class="img-thumbnail" style="max-width:220px">';


            }


        break;


    }




    bootstrap.Modal
    .getInstance(
        document.getElementById('mediaModal')
    )
    .hide();



});



function openGalleryPicker(){


    currentField='gallery';


    selectedMedia=[];



    document
    .querySelectorAll('.media-item')
    .forEach(function(card){


        card.classList.remove(

            'border-primary',
            'border-3',
            'shadow'

        );


    });



    new bootstrap.Modal(
        document.getElementById('mediaModal')
    ).show();



}


</script>