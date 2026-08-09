<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('frontend/images/logo_with_text.png') }}" type="image/png">
    <title>SENVATEC</title>
    <style>
        * { margin: 0; box-sizing: border-box; }
        body { background-color: #1d1f41; }
        .center-screen { display: flex; justify-content: center; align-items: center; min-height: 100vh; text-align: center; }
        .btn-custom {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            background-color: #ffffff;
            color: #000000;
            text-decoration: none;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            transition: background-color 0.15s ease;
        }
        .btn-custom:hover { background-color: rgb(53, 162, 80); color: #000000; }
    </style>
  </head>
  <body>
    <div class="center-screen">
        <div>
           <a href="#" class="btn-custom">Clic para Descargar desde AppStore</a>
        </div>
    </div>
  </body>
</html>
