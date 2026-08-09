<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de Suscripción - SENVATEC</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', 'Arial', sans-serif;
            color: #2c3e50;
            line-height: 1.6;
            background-color: #ffffff;
        }
        
        /* Header Styles */
        header {
            border-bottom: 3px solid #27ae60;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        header .row-one {
            display: table;
            width: 100%;
        }
        
        header .row-one div {
            display: table-cell;
            vertical-align: top;
        }
        
        header .row-one .logo-reporte {
            width: 18%;
            padding: 0;
            margin: 0;
            text-align: right;
        }
        
        header .row-one .logo-reporte img {
            width: 100px;
            height: 100px;
            margin: 0;
            padding: 0;
            vertical-align: middle;
            display: inline-block;
        }
        
        header .row-one .info-sucursal {
            width: 55%;
            padding: 5px 0 5px 5px;
        }
        
        header .row-one .info-sucursal p {
            margin: 3px 0;
            font-size: 11px;
            color: #555;
            line-height: 1.5;
        }
        
        header .row-one .info-sucursal p:first-child {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        
        header .row-one .info-sucursal p b {
            color: #2c3e50;
            font-weight: 600;
        }
        
        header .row-one .tittle-reporte {
            width: 30%;
            text-align: right;
            padding-top: 5px;
            padding-right: 15px;
            font-size: 10px;
            color: #7f8c8d;
            font-weight: 500;
        }
        
        /* Body Styles */
        .body {
            padding: 0 15px;
        }
        
        .body > p {
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin: 5px 0 10px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #27ae60;
        }
        
        /* Table Styles */
        .body table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .body table thead tr th {
            padding: 12px 8px;
            font-size: 11px;
            background-color: #27ae60;
            color: white;
            text-transform: uppercase;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            border: 1px solid #27ae60;
            letter-spacing: 0.5px;
        }
        
        .body table tbody tr td {
            border: 1px solid #e0e0e0;
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            font-size: 10px;
            text-align: left;
            padding: 2px 2px;
            vertical-align: top;
            background-color: #ffffff;
        }
        
        .body table tbody tr:nth-child(even) td {
            background-color: #f8f9fa;
        }
        
        .body table tbody tr td p {
            margin: 5px 0;
            line-height: 1.5;
        }
        
        .body table tbody tr td p span {
            font-weight: 600;
            color: #2c3e50;
            font-size: 10px;
        }
        
        /* Badge Styles */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .badge-success {
            background-color: #27ae60;
            color: white;
        }
        
        .badge-danger {
            background-color: #e74c3c;
            color: white;
        }
        
        /* Utility Classes */
        .normal-text {
            font-size: 11px;
        }
        
        .medium {
            max-width: 60px;
            min-width: 60px;
        }
    </style>
</head>

<body>
    <header>
        <div class="row-one">
            <div class="logo-reporte">
                <img src="{{ public_path('images/logo_circle.png') }}" alt="logo_senvatec">
            </div>
            <div class="info-sucursal">
                <p>SENVATEC</p>                   
                <p><b>Dirección:</b> Av. Cristo Redentor, 7mo Anillo Nro. 6680, Santa Cruz de la Sierra, Bolivia</p>
                <p><b>Teléfono:</b> 3 3438010</p>
                <p><b>Celular:</b> +591 71094422</p>
                <p><b>Website:</b> senvatec.com</p>
            </div>
            <div class="tittle-reporte">
                <strong>Fecha de emisión:</strong><br>
                {{date('d/m/Y H:i')}}
            </div>
        </div>
    </header>
    <div class="body">
        @if ($usersSubscription->user->user_type == 1) 
            <p>Datos de la Suscripción / Usuario Propietario</p>
        @elseif($usersSubscription->user->user_type == 2)
            <p>Datos de la Suscripción / Usuario Administrador</p>
        @elseif($usersSubscription->user->user_type == 3)
            <p>Datos de la Suscripción / Usuario Dependiente</p>
        @else
            <p>Datos de la Suscripción</p>    
        @endif
        <table>
            <thead>
                <tr>
                    <th scope="col">Empresa</th>
                    <th scope="col">Suscriptor</th>
                    <th scope="col">Fecha caducidad</th>                    
                    <th scope="col">Prorroga</th>
                    <th scope="col">Deudas por</th> 
                    <th scope="col">Acerca del Usuario</th>     
                </tr>
            </thead>
            <tbody>
                @if ($usersSubscription!=null)
                    
                        <tr>
                            <td>
                                <p><span style="font-weight: bold">Razón Social: </span> <br>{{$usersSubscription->subscription->business_name}}</p>
                                <p><span style="font-weight: bold">Nit: </span><br>{{$usersSubscription->subscription->nit}}</p>
                            </td>
                            <td>
                                <p><span style="font-weight: bold">Cliente: </span><br> {{$usersSubscription->user->name}}</p>
                                <p><span style="font-weight: bold">Correo: </span><br>{{$usersSubscription->user->email}}</p>
                            </td>
                            <td>
                                <p><span style="font-weight: bold">Inicio: </span><br> {{$usersSubscription->subscription->start_date}}</p>
                                <p><span style="font-weight: bold">Expiración: </span><br>{{$usersSubscription->subscription->date_expiry}}</p>
                            </td>
                            <td>
                                <p><span style="font-weight: bold">Inicio: </span><br> {{$usersSubscription->subscription->temporary_extension_start}}</p>
                                <p><span style="font-weight: bold">Final: </span><br>{{$usersSubscription->subscription->temporary_extension_end}}</p>
                            </td>
                            <td>
                                <p>
                                    <span style="font-weight: bold">Renovación: </span><br> 
                                    @if($usersSubscription->subscription->is_it_paid_renewal==1)
                                     Sin deudas
                                    @elseif ($usersSubscription->subscription->is_it_paid_renewal==0)
                                     Deuda pendiente
                                    @endif                                    
                                </p>
                                <p>
                                    <span style="font-weight: bold">Nuevos usuarios: </span><br> 
                                    @if($usersSubscription->subscription->is_it_paid_user==1)
                                     Sin deudas
                                    @elseif ($usersSubscription->subscription->is_it_paid_user==0)
                                     Deuda pendiente
                                    @endif                                    
                                </p>
                            </td>
                            <td>
                                <p>
                                    <span style="font-weight: bold">Estado: </span><br>
                                @switch( $usersSubscription->user->state)
                                    @case(0)
                                        <span class="badge badge-danger">Inactivo</span>
                                    @break
                                    @case(1)
                                        <span class="badge badge-success">Activo</span>
                                    @break                       
                                @endswitch
                                </p>
                                <p>
                                    <span style="font-weight: bold">Tipo de suscripción: </span><br> 
                                    {{$usersSubscription->subscription->subscription->name}}
                                </p>
                            </td>                            
                        </tr>
                                       
                @endif
            </tbody>
        </table>
        <br>
        
    </div>
    
    
</body>
</html>
