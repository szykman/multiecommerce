let uploading = false;
const dropArea = document.getElementById('drop-area');
const input = document.getElementById('file-input');
const select = document.getElementById('select-files');
const preview = document.getElementById('preview');
const upload = document.getElementById('upload-btn');

let files = [];


select.onclick = function(){

    input.click();

};


input.onchange = function(e){

    addFiles(e.target.files);

};


dropArea.ondragover = function(e){

    e.preventDefault();

    dropArea.classList.add('bg-light');

};


dropArea.ondragleave = function(){

    dropArea.classList.remove('bg-light');

};


dropArea.ondrop = function(e){

    e.preventDefault();

    dropArea.classList.remove('bg-light');

    addFiles(e.dataTransfer.files);

};



function addFiles(newFiles){


    for(let file of newFiles){


const allowed = [
    'image/',
    'video/',
    'audio/',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument',
    'application/zip'
];


let valid = allowed.some(type =>
    file.type.startsWith(type)
);


if(!valid){

    alert(
        'Arquivo não permitido: '+
        file.name
    );

    continue;

}

let index = files.length;

files.push(file);

        let col=document.createElement('div');

        col.className="col-md-3 mb-3";


        let content='';


        if(file.type.startsWith('image/')){


            let url=URL.createObjectURL(file);


            content=`

            <img src="${url}"
            class="img-fluid rounded mb-2"
            style="height:150px;object-fit:cover">

            `;


        }else{


            content=`

            <i class="bi bi-file-earmark fs-1"></i>

            `;


        }



        col.innerHTML=`

        <div class="card">

        <div class="card-body text-center">


        ${content}


        <small>

        ${file.name}

        </small>
<button
type="button"
class="btn btn-sm btn-danger mt-2 remove-file"
data-index="${index}">

<i class="bi bi-trash"></i>

</button>

        <br>


        <small class="text-muted">

        ${(file.size/1024).toFixed(1)} KB

        </small>


        </div>

        </div>


        `;


        preview.appendChild(col);


    }


    upload.disabled=false;


}






upload.onclick=function(){

 if(uploading){
        return;
    }

    uploading = true;

    upload.disabled = true;
    upload.innerHTML =
    '<i class="bi bi-hourglass-split"></i> Enviando...';

    let formData=new FormData();


    files.forEach(file=>{

        formData.append(
            'files[]',
            file
        );

    });



    fetch(
        '/admin/media/upload',
        {

        method:'POST',

        headers:{

            'X-CSRF-TOKEN':
            document
            .querySelector('meta[name="csrf-token"]')
            .content

        },

        body:formData

        }

    )

    .then(response=>response.json())

    .then(data=>{


        if(data.success){


            alert(
                'Arquivos enviados!'
            );


            window.location.href=
            '/admin/media';


        }


    })

    .catch(error=>{


        console.log(error);


        alert(
            'Erro no upload'
        );


    });


};


document.addEventListener(
'click',
function(e){

if(e.target.closest('.remove-file')){


let index =
e.target.closest('.remove-file')
.dataset.index;


files.splice(index,1);


e.target.closest('.col-md-3').remove();


if(files.length===0){

upload.disabled=true;

}

}

});
