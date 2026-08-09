# Códigos De Iconos Meteorológicos

Este documento describe el significado de los códigos de iconos de pronóstico usados por la API y consumidos por la app móvil.

La lógica base se encuentra en:

- `app/Http/Controllers/Api/v1/ForecastApiController.php`
- `app/Http/Controllers/ForecastC.php`

## Formato Del Código

Cada código tiene esta estructura:

`<número><sufijo>`

Ejemplos:

- `1a`
- `5b`
- `10a`

Donde:

- El número define la condición meteorológica principal.
- El sufijo indica si corresponde a día o noche.

## Sufijos Día / Noche

- `a`: diurno, entre `06:00` y `17:59`
- `b`: nocturno, entre `18:00` y `05:59`

## Tabla De Significados

| Código base | Significado general | Origen meteorológico |
| --- | --- | --- |
| `1` | Despejado | Cielo casi limpio |
| `2` | Poco nuboso | Nubosidad baja |
| `3` | Parcialmente nublado | Nubosidad moderada baja |
| `4` | Mayormente nublado | Nubosidad alta |
| `5` | Cubierto | Cielo muy cubierto |
| `6` | Lluvia | `RA()` |
| `7` | Llovizna | `DZ()` |
| `8` | Chubascos | `SH()` |
| `9` | Tormenta eléctrica | `TS()` |
| `10` | Nieve | `SN()` |
| `11` | Granizo / evento severo | `GR20()` |

## Detalle Por Código

### 1a / 1b

- `1a`: despejado de día
- `1b`: despejado de noche

Se usa cuando el índice cae entre `0` y `1`.

### 2a / 2b

- `2a`: poco nuboso de día
- `2b`: poco nuboso de noche

Se asocia a nubosidad baja.

### 3a / 3b

- `3a`: parcialmente nublado de día
- `3b`: parcialmente nublado de noche

Se asocia a nubosidad intermedia.

### 4a / 4b

- `4a`: mayormente nublado de día
- `4b`: mayormente nublado de noche

Se asocia a nubosidad alta, pero no completamente cubierta.

### 5a / 5b

- `5a`: cubierto de día
- `5b`: cubierto de noche

Se usa para cielo muy cubierto.

### 6a / 6b

- `6a`: lluvia de día
- `6b`: lluvia de noche

Proviene de la función `RA($prec)`.

Rango usado:

- precipitación entre `0.9` y `60`

### 7a / 7b

- `7a`: llovizna de día
- `7b`: llovizna de noche

Proviene de la función `DZ($prec)`.

Rango usado:

- precipitación entre `0.55` y `0.89`

### 8a / 8b

- `8a`: chubascos de día
- `8b`: chubascos de noche

Proviene de `SH($prec, $li)`.

Condición usada:

- `prec >= 0.78`
- `li <= -2.5`

### 9a / 9b

- `9a`: tormenta eléctrica de día
- `9b`: tormenta eléctrica de noche

Proviene de `TS($prec, $li)`.

Condición usada:

- `prec >= 1`
- `li < -3`

### 10a / 10b

- `10a`: nieve de día
- `10b`: nieve de noche

Proviene de `SN($temp, $prec)`.

Condición usada:

- `temp <= 1.4`
- `prec >= 0.55`

### 11a / 11b

- `11a`: granizo / evento severo de día
- `11b`: granizo / evento severo de noche

Proviene de `GR20($prec, $li)`.

Condiciones severas usadas:

- `prec >= 0.78` y `li <= -8`
- o bien escenarios más intensos con `li < -12.1`

## Cómo Se Decide El Icono

La lógica del backend toma el máximo entre varios índices meteorológicos:

- llovizna: `DZ($prec)`
- lluvia: `RA($prec)`
- nieve: `SN($temp, $prec)`
- chubascos: `SH($prec, $li)`
- tormenta: `TS($prec, $li)`
- granizo: `GR20($prec, $li)`
- nubosidad: `SKY($nubes)`

Luego ese valor se transforma al código base (`1` a `11`) y finalmente se le agrega:

- `a` si es de día
- `b` si es de noche

## Diferencia Entre Iconos Diarios Y Horarios

- Los iconos diarios suelen salir ya resumidos como códigos tipo `1a`, `6b`, etc.
- Los iconos horarios pueden venir como valor numérico base y la app móvil puede normalizarlos a `a` o `b` según la hora del registro.

## Referencia Rápida

- `1`: despejado
- `2`: poco nuboso
- `3`: parcialmente nublado
- `4`: mayormente nublado
- `5`: cubierto
- `6`: lluvia
- `7`: llovizna
- `8`: chubascos
- `9`: tormenta eléctrica
- `10`: nieve
- `11`: granizo
