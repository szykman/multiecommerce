<!DOCTYPE html>
<html>
<head>
    <title>MultiEcommerce Admin</title>
</head>
<body>

<h1>Login Admin</h1>

<form method="POST" action="/admin/login">
    @csrf

    <input type="email" name="email" placeholder="Email">

    <input type="password" name="password" placeholder="Senha">

    <button type="submit">
        Entrar
    </button>
</form>

</body>
</html>
