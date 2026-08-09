<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de Mantenimiento</title>
    <style>

        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;500;600;700;800&family=Roboto:wght@100;300;400;500;700;900&display=swap');
        body{
            font-family: 'Open Sans', sans-serif;
        }
        header .row-one div{
            display: inline-block;
        }
        header .row-one .logo-reporte{
            width: 12%;
            height: 100px;
            position: relative;
        }
        header .row-one .logo-reporte img {
            width: 70px;
            height:70px;
            position: absolute;
            top:20px;
        }
        
        header .row-one .info-sucursal{
            position: relative;
            width: 55%;
            height: 80px;
        }
        header .row-one .info-sucursal .container{
            position: absolute;
            top:0px;
        }
        header .row-one .info-sucursal .container p{
            margin: 0px;
            padding: 0px;
            font-size: 13px;
            font-family: 'Open Sans', sans-serif;
        }
        header .row-one .tittle-reporte{
            position: absolute;
            top: 0px;
            width: 31%;
            font-size: 11px;
            font-family: 'Open Sans', sans-serif;
            font-weight: 300px;
            text-align: right
        }
        
        header .row-two {
            width: 100%;
            padding: 10px 0px 10px 0px;
            font-size: 16px;
            text-align: center;
            text-transform:uppercase;
            font-family: 'Open Sans','sans-serif';
            color: rgb(20, 33, 87);
        }
        
        .body table{
            border-collapse:collapse;
            border: none;
            width: 100%;
        }
        .body table thead tr th{
            padding: 10px 2px 10px 2px;
            font-size: 12px;
            background: rgb(20, 33, 87);
            color:white;
            text-transform: uppercase;
            font-weight: bold;
            font-family: 'Open Sans', sans-serif;
            border: 1px solid black;
        }
        
        .body table tbody tr th,
        .body table tbody tr td{
            border: 1px solid black;
            font-family: 'Open Sans', sans-serif;
            font-weight: 300px;
            font-size: 11px;
            text-transform:capitalize;
            text-align: center;
            padding: 10px 0px 10px 0px;
        }
        .normal-text{
            font-size: 11px;
        }
        .medium{
            max-width: 60px;
            min-width: 60px;
        }

        .centered {
            text-align: center;
            margin-top: 130px;
        }
        .centered img {
            display: inline-block;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <header>
        <div class="row-one">
            <div class="logo-reporte">
                <img src="{{ public_path('imgpf/logo_artguz.jpeg') }}"  alt="logo_artguz">
            </div>
            <div class="info-sucursal">
                <div class="container">
                    <p><B>SENVATEC</B></p>                   
                    <p><b>Dirección:</b>Av. Cristo redentor, 7mo anillo Nro. 6680, Santa Cruz de la Sierra, Bolivia </p>
                    <p><b>Teléfono:</b> 3 3438010 </p>
                    <p><b>Celular:</b> +591 71094422 </p>
                    <p><b>Website:</b> artguz.com </p>
                </div>
            </div>
            <div class="tittle-reporte">Reporte en fecha: {{date('Y-m-d H:i:s')}} </div>
        </div>
    </header>
    <div class="body">
    <p style="text-align: center;font-size: 14pt;font-weight: bold;">Informe de Mantenimiento</p>

    <p style="text-align: left;font-size: 12pt;font-weight: bold;"><span style="font-size: 12pt;font-weight: bold;">ESTACIÓN: </span><span style="font-size: 12pt;color:rgb(27, 74, 169);">{{$dataResponse->station->name}} </span></p>
        <table>
            <thead>
                <tr>                    
                    <th scope="col" style="font-size: 8pt">Orden Trabajo</th>
                    <th scope="col" style="font-size: 8pt">Nota de entrega</th> 
                    <th scope="col" style="font-size: 8pt">Mantenimineto en Fecha:</th>                   
                    <th scope="col" style="font-size: 8pt">Proximo mantenimiento</th>                    
                    <th scope="col" style="font-size: 8pt">Tipo de mantenimiento</th>                   
                </tr>
            </thead>
            <tbody>
                @if($dataResponse!=null)                    
                    <tr>                        
                        <td>{{$dataResponse->work_order_number}}</td>
                        <td>{{$dataResponse->delivery_note_number}}</td>
                        <td>{{$dataResponse->maintenance_date}}</td>
                        <td>{{$dataResponse->next_maintenance_date}}</td>                        
                        <td>
                            <p>Preventivo: {{$dataResponse->preventive_maintenance}}</p>
                            <p>Correctivo: {{$dataResponse->corrective_maintenance}}</p>
                        </td>
                    </tr>                    
                @else
                    <tr class="text-center">
                        <td class="text-center py-2" colspan="8"> Sin datos</td>
                    </tr>
                @endif
            </tbody>
        </table> 
        <div style="margin-top: 15px;"></div>
        <table>
            <thead>
                <tr>                    
                    <th scope="col" style="font-size: 8pt">Descripción tecnica</th>
                    <th scope="col" style="font-size: 8pt">Recomendaciones</th>                    
                </tr>
            </thead>
            <tbody>
                @if($dataResponse!=null)                    
                    <tr>
                        <td>{{$dataResponse->description}}</td>
                        <td>{{$dataResponse->recommendations}}</td>                       
                    </tr>                    
                @else
                    <tr class="justify-content-center text-center">
                        <td class="text-center py-2" colspan="8"> Sin datos</td>
                    </tr>
                @endif
            </tbody>
        </table>
       
        <!--table>
            <thead>
                <tr>                    
                    <th scope="col" style="font-size: 8pt">Antes del manenimiento</th>
                    <th scope="col" style="font-size: 8pt">Después del mantenimiento</th>                    
                </tr>
            </thead>
            <tbody>
                @if($dataResponse!=null)                    
                    <tr>
                        <td>
                            <img src="{{ public_path('storage/'.$dataResponse->image_before) }}" class="img-thumbnail" alt="image_before" width="50" >
                        </td>
                        <td> 
                            <img src="{{ public_path('storage/'.$dataResponse->image_after) }}" class="img-thumbnail" alt="image_after" width="50" >
                        </td>                       
                    </tr>                    
                @else
                    <tr class="justify-content-center text-center">
                        <td class="text-center py-2" colspan="8"> Sin datos</td>
                    </tr>
                @endif
            </tbody>
        </table-->

        <div class="centered">
            <img src="{{ public_path('storage/'.$dataResponse->image_signature) }}" class="img-thumbnail" alt="{{ asset('storage/'.$dataResponse->image_signature) }}" width="250" >
            <div>______________________________</div>
            <div>Recibo</div>
        </div>
        
    </div>   
    
</body>
</html>
