<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de estación</title>
    <style>
        body {
            margin: 0;
            color: #1f2937;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.45;
        }

        .page {
            padding: 4px 30px 24px;
        }

        .header {
            border: 1px solid #d1fae5;
            border-radius: 12px;
            background: #ffffff;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .header-accent {
            height: 4px;
            background: #047857;
        }

        .header-content {
            width: 100%;
            border-collapse: collapse;
        }

        .header-content td {
            vertical-align: top;
            padding: 8px 8px;
        }

        .logo-cell {
            width: 62px;
            vertical-align: middle !important;
            padding-right: 2px !important;
        }

        .logo-box {
            width: 58px;
            height: 58px;
            border: 1px solid #a7f3d0;
            border-radius: 50%;
            background: #f0fdf4;
            text-align: center;
            vertical-align: middle;
        }

        .logo-box img {
            width: 50px;
            height: 50px;
            margin-top: 3px;
        }

        .company-name {
            margin: 0 0 4px;
            font-size: 17px;
            font-weight: bold;
            color: #065f46;
            letter-spacing: 0.3px;
        }

        .company-meta {
            margin: 0;
            font-size: 9px;
            color: #374151;
            line-height: 1.15;
        }

        .company-meta + .company-meta {
            margin-top: 1px;
        }

        .report-date {
            margin: 0 0 10px;
            text-align: right;
            font-size: 9px;
            color: #065f46;
        }

        .report-date strong {
            color: #047857;
        }

        .hero {
            margin-bottom: 18px;
            padding: 12px 16px;
            border: 1px solid #d1fae5;
            border-radius: 16px;
            background: #f0fdf4;
        }

        .hero-title {
            margin: 0 0 4px;
            font-size: 16px;
            font-weight: bold;
            color: #065f46;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .hero-subtitle {
            margin: 0;
            font-size: 11px;
            color: #4b5563;
        }

        .section {
            margin-bottom: 18px;
        }

        .section-title {
            margin: 0 0 8px;
            font-size: 12px;
            font-weight: bold;
            color: #065f46;
            text-transform: uppercase;
            letter-spacing: 0.7px;
        }

        table.report-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #d1fae5;
            border-radius: 12px;
            overflow: hidden;
        }

        .report-table thead th {
            padding: 7px 8px;
            background: #047857;
            color: #ffffff;
            border-right: 1px solid rgba(255, 255, 255, 0.18);
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
        }

        .report-table thead th:last-child {
            border-right: none;
        }

        .report-table tbody td {
            padding: 13px 12px;
            border-top: 1px solid #d1fae5;
            border-right: 1px solid #ecfdf5;
            background: #ffffff;
            color: #1f2937;
            text-align: center;
            vertical-align: top;
        }

        .report-table tbody td:last-child {
            border-right: none;
        }

        .report-table tbody tr:nth-child(even) td {
            background: #f8fffb;
        }

        .empty-state {
            padding: 18px 12px;
            color: #6b7280;
            text-align: center;
        }

        .text-left {
            text-align: left !important;
        }

        .coordinate-line {
            margin: 0;
        }

        .coordinate-line + .coordinate-line {
            margin-top: 6px;
        }

        .status-pill {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .status-inactive {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .image-table td {
            padding: 16px 10px;
            background: #f8fffb;
        }

        .image-card {
            border: 1px solid #d1fae5;
            border-radius: 12px;
            background: #ffffff;
            padding: 10px;
        }

        .image-frame {
            height: 145px;
            border: 1px solid #d1fae5;
            border-radius: 10px;
            background: #f0fdf4;
            text-align: center;
            vertical-align: middle;
        }

        .image-frame img {
            max-width: 100%;
            max-height: 133px;
            margin-top: 6px;
        }

        .image-placeholder {
            padding: 50px 10px 0;
            font-size: 10px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .image-caption {
            margin-top: 8px;
            font-size: 10px;
            font-weight: bold;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $detail = $dataResponse ? $dataResponse->detail_station->first() : null;
    @endphp

    <div class="page">
        <div class="header">
            <div class="header-accent"></div>
            <table class="header-content">
                <tr>
                    <td class="logo-cell">
                        <div class="logo-box">
                            <img src="{{ public_path('images/logo_circle.png') }}" alt="SenvaTec">
                        </div>
                    </td>
                    <td>
                        <p class="company-name">SENVATEC</p>
                        <p class="company-meta"><strong>Dirección:</strong> Av. Cristo Redentor, 7mo anillo Nro. 6680, Santa Cruz de la Sierra, Bolivia</p>
                        <p class="company-meta"><strong>Teléfono:</strong> 3 3438010</p>
                        <p class="company-meta"><strong>Celular:</strong> +591 71094422</p>
                        <p class="company-meta"><strong>Website:</strong> artguz.com</p>
                    </td>
                </tr>
            </table>
        </div>

        <p class="report-date"><strong>Fecha del reporte:</strong> {{ date('Y-m-d H:i') }}</p>

        <div class="hero">
            <p class="hero-title">Informe de Estación</p>
            <p class="hero-subtitle">Resumen visual y técnico de la estación registrada en el sistema.</p>
        </div>

        <div class="section">
            <p class="section-title">Información general</p>
            <table class="report-table">
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Localidad</th>
                        <th scope="col">Dirección</th>
                        <th scope="col">Coordenadas geográficas</th>
                    </tr>
                </thead>
                <tbody>
                    @if($dataResponse)
                        <tr>
                            <td>{{ $dataResponse->name }}</td>
                            <td>{{ $dataResponse->location }}</td>
                            <td class="text-left">{{ $dataResponse->address }}</td>
                            <td>
                                <p class="coordinate-line"><strong>Latitud:</strong> {{ $dataResponse->latitude }}</p>
                                <p class="coordinate-line"><strong>Longitud:</strong> {{ $dataResponse->longitude }}</p>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="4" class="empty-state">Sin datos</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="section">
            <p class="section-title">Detalles técnicos</p>
            <table class="report-table">
                <thead>
                    <tr>
                        <th scope="col">Marca</th>
                        <th scope="col">Modelo</th>
                        <th scope="col">Fuente de alimentación</th>
                        <th scope="col">Fecha de instalación</th>
                        <th scope="col">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @if($dataResponse)
                        <tr>
                            <td>{{ $detail->brand ?? 'Sin registro' }}</td>
                            <td>{{ $detail->model ?? 'Sin registro' }}</td>
                            <td>{{ $detail->power_supply ?? 'Sin registro' }}</td>
                            <td>{{ $dataResponse->reg_date ?? 'Sin registro' }}</td>
                            <td>
                                <span class="status-pill {{ $dataResponse->state == 1 ? 'status-active' : 'status-inactive' }}">
                                    {{ $dataResponse->state == 1 ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="5" class="empty-state">Sin datos</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="section">
            <p class="section-title">Imágenes de referencia</p>
            <table class="report-table image-table">
                <thead>
                    <tr>
                        <th scope="col">Imagen 1</th>
                        <th scope="col">Imagen 2</th>
                        <th scope="col">Imagen 3</th>
                    </tr>
                </thead>
                <tbody>
                    @if($dataResponse)
                        <tr>
                            <td>
                                <div class="image-card">
                                    <div class="image-frame">
                                        @if(!empty($detail?->image1))
                                            <img src="{{ public_path('/storage/' . $detail->image1) }}" alt="Imagen 1 de estación">
                                        @else
                                            <div class="image-placeholder">Sin imagen disponible</div>
                                        @endif
                                    </div>
                                    <div class="image-caption">Referencia 1</div>
                                </div>
                            </td>
                            <td>
                                <div class="image-card">
                                    <div class="image-frame">
                                        @if(!empty($detail?->image2))
                                            <img src="{{ public_path('/storage/' . $detail->image2) }}" alt="Imagen 2 de estación">
                                        @else
                                            <div class="image-placeholder">Sin imagen disponible</div>
                                        @endif
                                    </div>
                                    <div class="image-caption">Referencia 2</div>
                                </div>
                            </td>
                            <td>
                                <div class="image-card">
                                    <div class="image-frame">
                                        @if(!empty($detail?->image3))
                                            <img src="{{ public_path('/storage/' . $detail->image3) }}" alt="Imagen 3 de estación">
                                        @else
                                            <div class="image-placeholder">Sin imagen disponible</div>
                                        @endif
                                    </div>
                                    <div class="image-caption">Referencia 3</div>
                                </div>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="3" class="empty-state">Sin datos</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
