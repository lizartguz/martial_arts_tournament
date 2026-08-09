<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña</title>
</head>
<body>
    <h1>Solicitud de restablecimiento de contraseña</h1>
    <p>Recibimos una solicitud para restablecer su contraseña. Puedes hacerlo haciendo clic en el siguiente enlace:</p>
    <a href="{{ url('password/reset/'.$token) }}">Restablecer contraseña</a>
    <p>Si no solicitó este restablecimiento de contraseña, ignore este correo electrónico.</p>
</body>
</html>