<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Formulario de Contacto</title>
</head>
<body>
    <h3>INFORMACIÓN DE CONTACTO</h3>
    <span><b>Nombres: </b>{{ $name }}</span>
    <br />
    <span><b>Apellidos: </b>{{ $lastname }}</span>
    <br />
    <span><b>Correo electrónico: </b>{{ $email }}</span>
    <br />
    <span><b>Empresa: </b>{{ $company }}</span>
    <br />
    <span><b>Teléfono móvil: </b>{{ $phone }}</span>
    <br />
    <span><b>Pais: </b>{{ $country }}</span>
    <br />
    <span><b>Ciudad: </b>{{ $city }}</span>
    <br />
    <span><b>Mensaje: </b>{{ $message_field }}</span>
</body>
</html>
