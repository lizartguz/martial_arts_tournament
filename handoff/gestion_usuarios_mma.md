# Gestión de usuarios MMA

## Objetivo

Implementar la vista administrativa para gestionar usuarios, estado de acceso y roles asignados respetando la jerarquía definida para la aplicación.

## Alcance funcional

- Ruta administrativa: `/admin/users`.
- Permiso de acceso: `users.view`.
- Permisos de operación:
  - `users.create` para crear usuarios.
  - `users.update` para editar usuarios.
  - `users.delete` para eliminar usuarios.
  - `users.assign_roles` para guardar la asignación de roles existentes.
- La pantalla lista usuarios visibles para el actor según la jerarquía de roles configurada.
- Los roles se crean desde código/base de datos por el programador; el panel solo asigna roles existentes a usuarios.

## Reglas de jerarquía

- `super_manager` puede ver, editar, eliminar y asignar roles a cualquier usuario.
- Un usuario administrador puede visualizar usuarios de su mismo nivel, pero no editarlos ni eliminarlos.
- Un usuario administrador puede gestionar usuarios con roles inferiores.
- Un usuario no puede gestionar usuarios con roles superiores.
- Un usuario no puede eliminar su propia cuenta.
- La asignación de roles se filtra por jerarquía: solo se pueden asignar roles que el actor tiene permitido administrar.
- Si un request manipulado intenta enviar roles no permitidos, el componente descarta esos roles y bloquea el guardado si no queda ningún rol asignable.

## Vista

La pantalla mantiene el patrón visual administrativo:

- Filtros superiores por búsqueda, rol, estado y cantidad por página.
- Tabla compacta con usuario, contacto, roles, último ingreso, estado y acciones.
- Última columna con menú de acciones.
- Los usuarios visibles pero no gestionables muestran una acción de solo lectura por jerarquía.
- Modal Livewire para creación y edición.
- Modal de confirmación para eliminación.
- Mensajes de éxito o bloqueo mediante eventos Livewire compatibles con SweetAlert2.

## Validaciones

- Nombre, correo, estado, contraseña inicial y al menos un rol son obligatorios.
- En edición, la contraseña puede quedar vacía para conservar la actual.
- El correo debe ser único.
- Los roles deben existir y pasar la validación jerárquica.
- El estado debe ser `activo` o `inactivo`.

## Rendimiento

La consulta principal usa:

- `with('roles')` para evitar consultas N+1 al mostrar roles asignados.
- `whereHas('roles')` para filtrar usuarios visibles por jerarquía y por rol seleccionado.

## Auditoría

Las acciones de creación, edición, eliminación e intentos bloqueados por jerarquía se registran mediante `AuthorizationAuditLogger`.

## Traducciones

Todos los textos visibles del módulo se definen en:

```text
mma.admin.users
```

Los nombres visibles de roles se obtienen desde:

```text
mma.roles.names
```
