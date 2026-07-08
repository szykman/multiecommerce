<!DOCTYPE html>
<html>
<head>
    <title>Novo Produto</title>
</head>
<body>

<h1>Novo Produto</h1>

<form method="POST" action="/admin/products" enctype="multipart/form-data">
    @csrf

    <p>
        Nome<br>
        <input type="text" name="name" required>
    </p>

    <p>
        Descrição<br>
        <textarea name="description"></textarea>
    </p>

    <p>
        Preço<br>
        <input type="number" name="price" step="0.01" required>
    </p>

    <p>
        Estoque<br>
        <input type="number" name="stock" value="0" required>
    </p>

    <p>
        Imagem<br>
        <input type="file" name="image" accept="image/*">
    </p>

    <button type="submit">
        Salvar Produto
    </button>

</form>

</body>
</html>
