@extends('admin.layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Editar Produto</h2>

        <a href="{{ route('products.index') }}"
           class="btn btn-secondary">

            Voltar

        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form method="POST"
                  action="{{ route('products.update',$product) }}"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="mb-3">

                    <label class="form-label">

                       <b>Nome</b>

                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name',$product->name) }}"
                        required>

                </div>



                <div class="row">

                    <div class="col-md-6">

                        <div class="mb-3">

                            <label class="form-label">

                                <b>Categoria</b>

                            </label>

                            <select
                                name="category_id"
                                class="form-select">

                                <option value="">
                                    Sem categoria
                                </option>

                                @foreach($categories as $category)

                                    <option
                                        value="{{ $category->id }}"
                                        @selected(old('category_id',$product->category_id)==$category->id)>

                                        {{ $category->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>


  <div class="col-md-3">

                        <label class="form-label">

                            <b>Status</b>

                        </label>

                        <select
                            class="form-select"
                            name="active">

                            <option
                                value="1"
                                @selected($product->active)>

                                Ativo

                            </option>

                            <option
                                value="0"
                                @selected(!$product->active)>

                                Inativo

                            </option>

                        </select>

                    </div>




                    <div class="col-md-3">



    <div class="mb-3">

                            <label class="form-label">

                                <b>Estoque</b>

                            </label>

                            <input
                                type="number"
                                name="stock"
                                id="stock_input"
                                class="form-control"
                                value="{{ old('stock',$product->stock) }}"
                                {{ $product->has_variants ? 'readonly' : '' }}>

                            <small
                                id="stock_help"
                                class="text-muted"
                                style="{{ $product->has_variants ? '' : 'display:none;' }}">
                                Calculado automaticamente pela soma das variações ativas.
                            </small>

                        </div>

</div>

</div>
                  <!-- PREÇOS -->

<div class="row mb-4">


    <div class="col-md-3">

        <label class="form-label">
            <b>Preço</b>
        </label>

        <input
            type="number"
            step="0.01"
            name="price"
            class="form-control"
            value="{{ old('price',$product->price) }}">

    </div>



    <div class="col-md-3">

        <label class="form-label">
            <b>Preço Promocional</b>
        </label>

        <input
            type="number"
            step="0.01"
            name="sale_price"
            class="form-control"
            value="{{ old('sale_price',$product->sale_price ?? '') }}">

    </div>



    <div class="col-md-3">

        <label class="form-label">
            <b>Início Promoção</b>
        </label>

        <input
            type="datetime-local"
            name="promotion_start"
            class="form-control"
            value="{{ old('promotion_start', optional($product->promotion_start)->format('Y-m-d\TH:i')) }}">

    </div>



    <div class="col-md-3">

        <label class="form-label">
            <b>Fim Promoção</b>
        </label>

        <input
            type="datetime-local"
            name="promotion_end"
            class="form-control"
            value="{{ old('promotion_end', optional($product->promotion_end)->format('Y-m-d\TH:i')) }}">

    </div>


</div>
<div class="mb-4">

    <label class="form-label">

        <b>Imagem Principal</b>

    </label>

    <div id="media_preview">

        @if($product->image_thumbnail_url)

            <img
                id="preview_image"
                src="{{ $product->image_thumbnail_url }}"
                class="img-thumbnail"
                style="max-width:220px">

        @else

            <img
                id="preview_image"
                class="img-thumbnail"
                style="display:none;max-width:220px">

        @endif

    </div>

    <input
        type="file"
        name="image"
        class="form-control mb-2"
        onchange="previewLocalImage(event)">

    <button
        type="button"
        class="btn btn-outline-primary"
        onclick="openMediaPicker('image')">

        <i class="bi bi-images"></i>

        <b>Biblioteca de Mídia</b>

    </button>

    <input
        type="hidden"
        name="media_id"
        id="media_id"
        value="{{ old('media_id',$product->media_id) }}">

</div>





<hr class="my-4">

<h4>
    <i class="bi bi-images"></i>
    Galeria de Fotos
</h4>

<p class="text-muted">
    Adicione fotos adicionais do produto.
</p>

<div class="mb-3">

    <button
        type="button"
        class="btn btn-outline-primary"
        onclick="openGalleryPicker()">

        <i class="bi bi-images"></i>

        Biblioteca de Mídia

    </button>

<input
    type="file"
    id="gallery_upload"
    class="form-control mt-2"
    multiple>

<div id="gallery_upload_preview" class="row g-3 mt-3"></div>

{{--
    Aqui ficam os hidden inputs "gallery[]" das fotos NOVAS
    (upload direto ou selecionadas na biblioteca).
--}}
<div id="gallery_inputs"></div>




{{--
    Fotos JÁ SALVAS no produto. Cada card agora carrega seu
    próprio hidden input "gallery[]" dentro do mesmo elemento
    removido pelo botão "Excluir" — assim o array enviado no
    submit representa sempre o estado final da galeria
    (existentes que não foram excluídas + novas adicionadas),
    e não some mais nada que já estava salvo.
--}}
<div
    id="gallery_list"
    class="row g-3">


@foreach($product->gallery as $photo)

<div class="col-md-3 gallery-item"
     data-media="{{ $photo->media_id }}">

    <div class="card">

        <img
            src="{{ asset('storage/'.($photo->media->thumbnail ?: $photo->media->file)) }}"
            class="card-img-top"
            style="height:180px;object-fit:cover">

        <div class="card-body text-center">

            <button
                type="button"
                class="btn btn-danger btn-sm btn-remove-gallery">

                Excluir

            </button>

        </div>

    </div>

    <input
        type="hidden"
        name="gallery[]"
        value="{{ $photo->media_id }}">

</div>

@endforeach

</div>







@include('admin.media.modal')

   <div class="mb-3">

                    <label class="form-label">

                        <b>Descrição</b>

                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="5">{{ old('description',$product->description) }}</textarea>

                </div>


<hr class="my-4">

<h4>
    <i class="bi bi-diagram-3"></i>
    Variações do Produto
</h4>

<p class="text-muted">
    Cadastre opções (ex: Cor, Tamanho) para gerar combinações com
    preço e estoque próprios. O estoque do produto passa a ser
    calculado automaticamente enquanto isso estiver ativado.
</p>

<div class="form-check form-switch mb-2">

    <input
        class="form-check-input"
        type="checkbox"
        id="has_variants_toggle"
        {{ $product->has_variants ? 'checked' : '' }}>

    <label class="form-check-label" for="has_variants_toggle">
        <b>Este produto tem variações</b>
    </label>

</div>

<div
    id="variants_not_saved_hint"
    class="alert alert-warning py-2 px-3 small mb-3"
    style="display:none;">
    <i class="bi bi-exclamation-triangle"></i>
    Cadastre as opções abaixo e clique em <b>"Gerar variações"</b> para ativar de fato —
    apenas marcar esta caixa não salva nada sozinho.
</div>

<div id="variants_section" style="{{ $product->has_variants ? '' : 'display:none;' }}">

    <div id="options_builder">

        @foreach($product->options as $option)

        <div class="option-row card mb-3">

            <div class="card-body">

                <div class="row g-2 align-items-center">

                    <div class="col-md-3">
                        <input
                            type="text"
                            class="form-control option-name"
                            placeholder="Nome (ex: Cor)"
                            value="{{ $option->name }}">
                    </div>

                    <div class="col-md-8">
                        <input
                            type="text"
                            class="form-control option-values"
                            placeholder="Valores separados por vírgula (ex: Preto, Branco, Azul)"
                            value="{{ $option->values->pluck('value')->implode(', ') }}">
                    </div>

                    <div class="col-md-1 text-end">
                        <button
                            type="button"
                            class="btn btn-outline-danger btn-sm remove-option">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>

                </div>

            </div>

        </div>

        @endforeach

    </div>

    <button
        type="button"
        id="add_option_btn"
        class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-plus-circle"></i>
        Adicionar opção
    </button>

    <div class="mb-4">
        <button
            type="button"
            id="generate_variants_btn"
            class="btn btn-primary btn-sm">
            <i class="bi bi-magic"></i>
            Gerar variações
        </button>
    </div>

    <div
        id="variants_table_wrapper"
        style="{{ $product->variants->count() ? '' : 'display:none;' }}">

        <div class="table-responsive">

            <table class="table table-sm align-middle" id="variants_table">

                <thead>
                    <tr>
                        <th>Combinação</th>
                        <th style="width:140px">SKU</th>
                        <th style="width:120px">Preço</th>
                        <th style="width:120px">Preço Promo.</th>
                        <th style="width:100px">Estoque</th>
                        <th style="width:70px" class="text-center">Ativa</th>
                        <th style="width:50px"></th>
                    </tr>
                </thead>

                <tbody id="variants_table_body">

                    @foreach($product->variants as $variant)

                    <tr data-variant-id="{{ $variant->id }}">

                        <td>
                            {{ $variant->optionValues->pluck('value')->implode(' / ') }}
                        </td>

                        <td>
                            <input
                                type="text"
                                class="form-control form-control-sm variant-sku"
                                value="{{ $variant->sku }}">
                        </td>

                        <td>
                            <input
                                type="number"
                                step="0.01"
                                class="form-control form-control-sm variant-price"
                                value="{{ $variant->price }}">
                        </td>

                        <td>
                            <input
                                type="number"
                                step="0.01"
                                class="form-control form-control-sm variant-sale-price"
                                value="{{ $variant->sale_price }}">
                        </td>

                        <td>
                            <input
                                type="number"
                                class="form-control form-control-sm variant-stock"
                                value="{{ $variant->stock }}">
                        </td>

                        <td class="text-center">
                            <input
                                type="checkbox"
                                class="form-check-input variant-active"
                                {{ $variant->active ? 'checked' : '' }}>
                        </td>

                        <td>
                            <button
                                type="button"
                                class="btn btn-outline-danger btn-sm remove-variant">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        <button
            type="button"
            id="save_variants_btn"
            class="btn btn-success btn-sm">
            <i class="bi bi-check-circle"></i>
            Salvar Variações
        </button>

        <span id="variants_feedback" class="text-muted ms-3"></span>

    </div>

</div>


                <button
                    class="btn btn-success">

                    💾 Salvar Alterações

                </button>






            </form>

        </div>

    </div>

</div>

<script>

function previewLocalImage(event){

    document.getElementById('media_id').value='';

    const file=event.target.files[0];

    if(!file){
        return;
    }

    const preview=document.getElementById('media_preview');

    preview.innerHTML='';

    const img=document.createElement('img');

    img.src=URL.createObjectURL(file);

    img.className='img-thumbnail';

    img.style.maxWidth='220px';

    preview.appendChild(img);

}

</script>


<script>

// Remove um card da galeria (existente ou recém-adicionado)
// junto com o hidden input "gallery[]" correspondente.
function removeGalleryItem(button){

    let col = button.closest('.col-md-3');

    if(col){
        col.remove();
    }

}

</script>


<script>

document.getElementById('gallery_upload').addEventListener('change', function(e){

    let files = e.target.files;
    if(!files.length){
        return;
    }

    let preview = document.getElementById('gallery_upload_preview');
    let inputs = document.getElementById('gallery_inputs');
    let formData = new FormData();

    preview.innerHTML = '';

    for(let i = 0; i < files.length; i++){

        formData.append('files[]', files[i]);

        let img = document.createElement('img');
        img.src = URL.createObjectURL(files[i]);
        img.className = 'img-thumbnail';
        img.style.width = '180px';
        img.style.height = '180px';
        img.style.objectFit = 'cover';

        let col = document.createElement('div');
        col.className = 'col-md-3';
        col.appendChild(img);

        preview.appendChild(col);
    }

    fetch("{{ route('media.upload') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {

        if(!data.success){
            alert('Erro no upload');
            return;
        }

        if(data.media){

            let html = '';

            data.media.forEach(function(item){
                html += `<input type="hidden" name="gallery[]" value="${item.id}">`;
            });

            // += para NÃO apagar os hidden inputs já existentes
            // (fotos salvas anteriormente + outros lotes de upload)
            inputs.innerHTML += html;

        }

    })
    .catch(function(err){
        console.error('Erro na requisição de upload:', err);
        alert('Falha na comunicação com o servidor.');
    });

});

</script>

<script>

document.addEventListener('click', function(e){

    if(!e.target.classList.contains('btn-remove-gallery')){
        return;
    }

    let item = e.target.closest('.gallery-item');

    if(!item){
        return;
    }

    item.remove();

});

</script>


<script>

/*
|--------------------------------------------------------------------------
| Variações do Produto
|--------------------------------------------------------------------------
*/

const productId = {{ $product->id }};

const routes = {
    generate: "{{ route('products.variants.generate', $product) }}",
    update: "{{ route('products.variants.update', $product) }}",
    disable: "{{ route('products.variants.disable', $product) }}",
    destroy: "{{ route('products.variants.destroy', ':id') }}",
};

const csrfToken = '{{ csrf_token() }}';

const hasVariantsToggle = document.getElementById('has_variants_toggle');
const variantsSection = document.getElementById('variants_section');
const optionsBuilder = document.getElementById('options_builder');
const addOptionBtn = document.getElementById('add_option_btn');
const generateBtn = document.getElementById('generate_variants_btn');
const saveVariantsBtn = document.getElementById('save_variants_btn');
const variantsTableWrapper = document.getElementById('variants_table_wrapper');
const variantsTableBody = document.getElementById('variants_table_body');
const variantsFeedback = document.getElementById('variants_feedback');
const stockInput = document.getElementById('stock_input');
const stockHelp = document.getElementById('stock_help');
const notSavedHint = document.getElementById('variants_not_saved_hint');

// Reflete se has_variants já está confirmado no banco (renderizado
// pelo servidor) ou se é uma marcação ainda não persistida.
let variantsPersisted = {{ $product->has_variants ? 'true' : 'false' }};

function updateStockDisplay(stock){

    if(stock === undefined || stock === null){
        return;
    }

    stockInput.value = stock;
}

function setStockReadonly(readonly){

    if(readonly){
        stockInput.setAttribute('readonly', 'readonly');
        stockHelp.style.display = '';
    }else{
        stockInput.removeAttribute('readonly');
        stockHelp.style.display = 'none';
    }
}

function refreshNotSavedHint(){
    notSavedHint.style.display =
        (hasVariantsToggle.checked && !variantsPersisted) ? '' : 'none';
}

hasVariantsToggle.addEventListener('change', function(){

    if(this.checked){

        variantsSection.style.display = '';
        setStockReadonly(true);
        refreshNotSavedHint();

    }else{

        variantsSection.style.display = 'none';
        setStockReadonly(false);
        notSavedHint.style.display = 'none';

        fetch(routes.disable, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
        })
        .then(function(){
            variantsPersisted = false;
        })
        .catch(function(err){
            console.error('Erro ao desativar variações:', err);
        });

    }

});

function createOptionRow(name, values){

    const div = document.createElement('div');
    div.className = 'option-row card mb-3';

    div.innerHTML = `
        <div class="card-body">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input type="text" class="form-control option-name"
                        placeholder="Nome (ex: Cor)" value="${name || ''}">
                </div>
                <div class="col-md-8">
                    <input type="text" class="form-control option-values"
                        placeholder="Valores separados por vírgula (ex: Preto, Branco, Azul)"
                        value="${values || ''}">
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-option">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

    return div;
}

addOptionBtn.addEventListener('click', function(){
    optionsBuilder.appendChild(createOptionRow('', ''));
});

optionsBuilder.addEventListener('click', function(e){

    if(!e.target.closest('.remove-option')){
        return;
    }

    const row = e.target.closest('.option-row');

    if(row){
        row.remove();
    }

});

function collectOptionsFromForm(){

    const rows = optionsBuilder.querySelectorAll('.option-row');
    const options = [];

    rows.forEach(function(row){

        const name = row.querySelector('.option-name').value.trim();
        const rawValues = row.querySelector('.option-values').value;

        const values = rawValues
            .split(',')
            .map(function(v){ return v.trim(); })
            .filter(function(v){ return v.length > 0; });

        if(name && values.length){
            options.push({ name: name, values: values });
        }

    });

    return options;
}

function renderVariantRow(variant){

    const tr = document.createElement('tr');
    tr.dataset.variantId = variant.id;

    const combination = (variant.option_values || [])
        .map(function(ov){ return ov.value; })
        .join(' / ');

    const price = variant.price ?? '';
    const salePrice = variant.sale_price ?? '';
    const stock = variant.stock ?? 0;
    const sku = variant.sku ?? '';
    const active = variant.active ? 'checked' : '';

    tr.innerHTML = `
        <td>${combination}</td>
        <td><input type="text" class="form-control form-control-sm variant-sku" value="${sku}"></td>
        <td><input type="number" step="0.01" class="form-control form-control-sm variant-price" value="${price}"></td>
        <td><input type="number" step="0.01" class="form-control form-control-sm variant-sale-price" value="${salePrice}"></td>
        <td><input type="number" class="form-control form-control-sm variant-stock" value="${stock}"></td>
        <td class="text-center"><input type="checkbox" class="form-check-input variant-active" ${active}></td>
        <td><button type="button" class="btn btn-outline-danger btn-sm remove-variant"><i class="bi bi-x-lg"></i></button></td>
    `;

    return tr;
}

generateBtn.addEventListener('click', function(){

    const options = collectOptionsFromForm();

    if(!options.length){
        alert('Adicione ao menos uma opção com um valor antes de gerar as variações.');
        return;
    }

    generateBtn.disabled = true;
    generateBtn.textContent = 'Gerando...';

    fetch(routes.generate, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ options: options }),
    })
    .then(function(response){ return response.json(); })
    .then(function(data){

        if(!data.success){
            alert('Erro ao gerar variações.');
            return;
        }

        variantsTableBody.innerHTML = '';

        data.variants.forEach(function(variant){
            variantsTableBody.appendChild(renderVariantRow(variant));
        });

        variantsTableWrapper.style.display = '';

        setStockReadonly(true);
        hasVariantsToggle.checked = true;
        variantsPersisted = true;
        refreshNotSavedHint();
        updateStockDisplay(data.stock);

        variantsFeedback.textContent = 'Variações geradas com sucesso.';

    })
    .catch(function(err){
        console.error('Erro ao gerar variações:', err);
        alert('Falha na comunicação com o servidor.');
    })
    .finally(function(){
        generateBtn.disabled = false;
        generateBtn.innerHTML = '<i class="bi bi-magic"></i> Gerar variações';
    });

});

variantsTableBody.addEventListener('click', function(e){

    if(!e.target.closest('.remove-variant')){
        return;
    }

    const row = e.target.closest('tr');
    const variantId = row.dataset.variantId;

    if(!confirm('Remover esta variação?')){
        return;
    }

    fetch(routes.destroy.replace(':id', variantId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
    })
    .then(function(response){ return response.json(); })
    .then(function(data){

        if(!data.success){
            alert('Erro ao remover variação.');
            return;
        }

        row.remove();
        updateStockDisplay(data.stock);

    })
    .catch(function(err){
        console.error('Erro ao remover variação:', err);
    });

});

saveVariantsBtn.addEventListener('click', function(){

    const rows = variantsTableBody.querySelectorAll('tr');

    if(!rows.length){
        alert('Não há variações para salvar.');
        return;
    }

    const variants = [];

    rows.forEach(function(row){

        variants.push({
            id: parseInt(row.dataset.variantId, 10),
            sku: row.querySelector('.variant-sku').value || null,
            price: parseFloat(row.querySelector('.variant-price').value) || 0,
            sale_price: row.querySelector('.variant-sale-price').value
                ? parseFloat(row.querySelector('.variant-sale-price').value)
                : null,
            stock: parseInt(row.querySelector('.variant-stock').value, 10) || 0,
            active: row.querySelector('.variant-active').checked,
        });

    });

    saveVariantsBtn.disabled = true;
    saveVariantsBtn.textContent = 'Salvando...';

    fetch(routes.update, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ variants: variants }),
    })
    .then(function(response){ return response.json(); })
    .then(function(data){

        if(!data.success){
            alert('Erro ao salvar variações.');
            return;
        }

        updateStockDisplay(data.stock);
        variantsFeedback.textContent = 'Variações salvas com sucesso.';

    })
    .catch(function(err){
        console.error('Erro ao salvar variações:', err);
        alert('Falha na comunicação com o servidor.');
    })
    .finally(function(){
        saveVariantsBtn.disabled = false;
        saveVariantsBtn.innerHTML = '<i class="bi bi-check-circle"></i> Salvar Variações';
    });

});

</script>

@endsection
