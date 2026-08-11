# Gestión de noticias MMA

## Objetivo

El módulo de noticias permite administrar comunicados, novedades, entrevistas y publicaciones públicas desde `/admin/news`. Las noticias pueden quedar en borrador, publicarse o archivarse, y cada registro mantiene slug público, resumen, contenido, imagen de portada y auditoría básica.

## Permisos

```text
news.view
news.create
news.update
news.delete
news.publish
```

- `news.view`: permite acceder al listado de noticias.
- `news.create`: permite crear nuevas publicaciones.
- `news.update`: permite editar contenido, slug, estado, destacado e imagen.
- `news.delete`: permite eliminar lógicamente la publicación y borrar su portada pública si existe.
- `news.publish`: permite publicar una noticia desde la acción rápida o guardarla con estado publicado.

La ruta usa `can:news.view`, el controlador valida con `Gate::authorize('news.view')` y el componente Livewire valida `news.view` en `mount()` y permisos específicos antes de crear, editar, publicar o eliminar.

## Flujo de vista

La vista sigue el patrón administrativo común:

- filtros superiores por búsqueda, estado, destacado, rango de publicación y cantidad por página;
- tabla con noticia, autor, fecha de publicación, estado, destacado y acciones;
- menú de acciones en la última columna;
- modal de creación/edición con validaciones por campo;
- confirmación modal antes de eliminar;
- mensajes de éxito mediante eventos Livewire compatibles con SweetAlert2.

## Reglas de slug

- El slug se genera automáticamente desde el título mientras el administrador no lo modifique manualmente.
- Si el administrador edita el slug, se normaliza con `Str::slug`.
- El slug debe cumplir el formato `^[a-z0-9]+(?:-[a-z0-9]+)*$`.
- El slug debe ser único en `news_posts`.
- El slug es la base para una URL pública futura de la noticia.

## Estados

```text
0 borrador
1 publicado
2 archivado
```

- Al publicar, `published_at` se asigna si todavía no existe.
- Al guardar con estado publicado se requiere `news.publish`.
- Al guardar como borrador o archivado, `published_at` se limpia para evitar mostrar contenido fuera del estado publicado.

## Imágenes

La imagen de portada se procesa con `App\Services\ImageUploadOptimizer` y se guarda en el disco público usando `uploads.public_images`.

Formatos permitidos:

```text
jpg
jpeg
png
webp
```

Tamaño máximo inicial: 5 MB.

Al reemplazar o eliminar una portada guardada bajo `storage/`, el archivo anterior se elimina del disco público.

## Rendimiento

El listado carga anticipadamente:

```text
createdBy
```

Los filtros trabajan directamente sobre columnas indexables o campos propios de `news_posts`, evitando recorridos en memoria.

## Traducciones

Todos los textos visibles usan claves bajo:

```text
mma.admin.news
```

Las claves están disponibles en español, inglés, portugués, francés, alemán y ruso. El español conserva tildes y acentos como idioma por defecto.
