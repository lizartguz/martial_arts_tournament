<?php

use App\Http\Controllers\PublicMediaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de medios publicos
|--------------------------------------------------------------------------
|
| Canal unico de lectura para las imagenes administradas. Viven en el disco
| privado (`storage/app/private/mma/...`) y nunca en `public/storage`, asi que
| la estructura real del storage no viaja en el HTML.
|
| Se registran fuera del grupo `web` a proposito: no necesitan sesion ni CSRF,
| y sin cookie de sesion la respuesta si puede declararse cacheable publica.
| El middleware global de cabeceras de seguridad si se aplica.
|
*/

Route::get('/media/{path}', [PublicMediaController::class, 'show'])
    ->where('path', '.*')
    ->name('media.public.show');
