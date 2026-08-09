# API de Automatizaciones IA

Esta documentación explica cómo consumir el endpoint de automatizaciones IA desde Postman, Flutter o cualquier cliente HTTP.

## Endpoint

Ruta:

```text
POST /api/v1/automations/execute
```

Ejemplo de URL local:

```text
http://127.0.0.1:8000/api/v1/automations/execute
```

Ejemplo en servidor:

```text
https://environmental.artguz.com/api/v1/automations/execute
```

## Método

```text
POST
```

## Headers recomendados

```text
Content-Type: application/json
Accept: application/json
```

## Body de entrada

Actualmente el endpoint recibe un campo principal:

```json
{
  "text": "clima"
}
```

También puedes enviar frases más naturales:

```json
{
  "text": "Explícame el clima por favor"
}
```

```json
{
  "text": "Quiero saber sobre riego para agricultura"
}
```

## Cómo decide qué prompt usar

El sistema revisa el texto recibido y busca coincidencias con las automatizaciones registradas.

Ejemplos actuales:

- `clima` o frases relacionadas: usa la automatización de clima
- `riego` o frases relacionadas: usa la automatización de riego

Si no encuentra una coincidencia válida, devuelve error `422`.

## Ejemplo en Postman

### 1. Crear request

- Método: `POST`
- URL: `http://127.0.0.1:8000/api/v1/automations/execute`

### 2. Headers

```text
Content-Type: application/json
Accept: application/json
```

### 3. Body

Selecciona `raw` y `JSON`, luego envía:

```json
{
  "text": "clima"
}
```

O:

```json
{
  "text": "Quiero saber sobre riego"
}
```

## Respuesta exitosa

Ejemplo:

```json
{
  "success": true,
  "message": "Automation executed successfully.",
  "data": {
    "input": "clima",
    "normalized_input": "clima",
    "automation": {
      "key": "weather_concept",
      "instructions": "Eres un asistente educativo especializado en clima y meteorologia. Responde en espanol claro, breve y facil de entender, con un tono profesional.",
      "prompt": "Explica de forma breve que es el clima, cuales son sus elementos principales y por que es importante entenderlo en la vida diaria.",
      "meta": {
        "trigger": "clima",
        "normalized_trigger": "clima",
        "topic": "concepto sobre el clima"
      }
    },
    "provider": "openai",
    "model": "gpt-4.1-mini",
    "response": "El clima es el conjunto de condiciones atmosfericas que caracterizan una region durante largos periodos de tiempo..."
  }
}
```

## Qué campos te interesan normalmente

Los más útiles para frontend o app móvil suelen ser:

- `success`: indica si la operación fue exitosa
- `message`: mensaje general
- `data.automation.key`: automatización que respondió
- `data.provider`: proveedor IA usado
- `data.model`: modelo usado
- `data.response`: texto final generado por la IA

## Ejemplo de error por texto no soportado

```json
{
  "success": false,
  "message": "No automation is configured for the input [inventario].",
  "data": null
}
```

Código HTTP:

```text
422 Unprocessable Entity
```

## Ejemplo en Flutter

Usando el paquete `http`:

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

Future<void> ejecutarAutomatizacion() async {
  final url = Uri.parse(
    'https://environmental.artguz.com/api/v1/automations/execute',
  );

  final response = await http.post(
    url,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: jsonEncode({
      'text': 'clima',
    }),
  );

  final data = jsonDecode(response.body);

  if (response.statusCode == 200 && data['success'] == true) {
    final texto = data['data']['response'];
    print(texto);
  } else {
    print(data['message']);
  }
}
```

## Ejemplo de lectura de respuesta en Flutter

Si quieres extraer los datos principales:

```dart
final body = jsonDecode(response.body);

final success = body['success'];
final message = body['message'];
final automationKey = body['data']?['automation']?['key'];
final provider = body['data']?['provider'];
final model = body['data']?['model'];
final aiResponse = body['data']?['response'];
```

## Ejemplo para cURL

```bash
curl -X POST "https://environmental.artguz.com/api/v1/automations/execute" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"text\":\"clima\"}"
```

## Flujo interno resumido

1. El cliente envía `text`
2. Laravel valida la request
3. El sistema detecta qué automatización corresponde
4. Se usa el prompt configurado para esa automatización
5. Se consulta al proveedor IA configurado
6. Laravel devuelve el resultado en JSON

## Dónde agregar nuevas automatizaciones

Si mañana quieres otro tema:

1. crear una nueva clase en `app/Automation/Actions/`
2. definir sus palabras clave y prompt
3. registrarla en `app/Automation/AutomationRegistry.php`

No necesitas crear una ruta nueva para cada prompt.
