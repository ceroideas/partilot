# Análisis de cambios aplicables – Informe técnico del cliente

**Fecha:** 29 de enero de 2026  
**Alcance:** Comparación del informe técnico (22 ene 2026) con el estado actual del sistema (sipart).

Este documento indica qué puntos del informe técnico **ya están hechos**, cuáles **faltan o difieren** y cuáles **se pueden aplicar** en el código actual.

---

## 1. Módulo de vendedores (core)

### 1.1. Estabilización de ficha de vendedor (`sellers/show.blade.php`)

| Punto del informe | Estado actual | Acción recomendada |
|-------------------|---------------|--------------------|
| **Scope JS / variables** | Hay uso de `participacionesAsignadas` sin declaración global y `let participacionesAsignadas` dentro de un handler (aprox. líneas 1305, 1351, 1369). Riesgo de TDZ y duplicación de variable. | **Aplicar:** Declarar `participacionesAsignadas` y `participacionesDisponibles` (y similares) en scope superior (p. ej. al inicio del bloque de asignación), y eliminar el `let` duplicado dentro del handler. |
| **Máquina de estados UI (mode-data / mode-ops)** | No existe lógica explícita `mode-data` vs `mode-ops` en el sidebar para alternar entre “gestión/bloqueo” y “contexto usuario/entidad” según pestaña. | **Valorar:** Revisar con el cliente si tienen mockups o flujo exacto. Si no, dejar para una segunda fase y solo documentar como mejora futura. |
| **Edición inline (readonly toggle)** | No se ha revisado en detalle el script de desbloqueo de inputs ni posibles eventos jQuery mal anidados en `sellers/show`. | **Revisar:** Comprobar en `sellers/edit` y en `sellers/show` si hay edición inline; si existe, unificar lógica y asegurar que no haya listeners duplicados o mal anidados. |

### 1.2. Lógica de negocio y modelado (`Seller.php`, `SellerController.php`)

| Punto del informe | Estado actual | Acción recomendada |
|-------------------|---------------|--------------------|
| **Sincronización de grupos** | En `update` se hace `sync` si `group_id` existe y no está vacío; si no, se hace `detach`. En ediciones parciales (sin `group_id`) se desvincula el grupo. | **Aplicar:** Solo tocar grupos cuando `group_id` esté presente en la petición. Si no viene `group_id`, no modificar `groups()`. Si viene y está vacío, se puede hacer `detach` (elección de negocio). |
| **Propagación a `User`** | Al actualizar vendedor ya se actualiza el `User` vinculado (nombre, email, teléfono, etc.) cuando `user_id` existe. | **Hecho.** |
| **Cast de `status`** | En `Seller` hay `'status' => 'boolean'`. El informe pide estados multivalor (0: Inactivo, 1: Activo, 2: Pendiente, etc.). | **Aplicar:** Quitar el cast `'status' => 'boolean'` en `Seller` y adaptar accesores `status_text` / `status_class` (y vistas que usen `status`) para soportar 0, 1, 2. |
| **Accessors de deuda y totales** | No hay atributos virtuales `debt_amount` ni totales de participaciones en el modelo. | **Aplicar:** Añadir en `Seller` accessors (o atributos calculados) para `debt_amount` y totales de participaciones según reglas de negocio acordadas (liquidaciones, participaciones asignadas/vendidas, etc.). |

### 1.3. Alta y persistencia (`SellerService.php`, migraciones)

| Punto del informe | Estado actual | Acción recomendada |
|-------------------|---------------|--------------------|
| **`user_id` nullable en `sellers`** | Migraciones ya tienen `user_id` nullable en `sellers`. | **Hecho.** |
| **Estados por defecto** | Externos: `status` por defecto viene de `$data['status'] ?? false` (0). Partilot pendientes: `status => false` (0). Informe: externos ACTIVOS (1), online PENDIENTES (2). | **Aplicar:** En `SellerService`: externos por defecto `status = 1`; partilot pendientes `status = 2`. Ajustar `createPartilotSeller` / `createExternalSeller` según corresponda. |
| **`user_id` para externos** | Se usa `user_id => 0` para externos y pendientes. Informe indica “vendedores offline” sin cuenta → `user_id` NULL. | **Valorar:** Usar `user_id => null` para externos y mantener `0` para “pendientes de vincular” si se sigue ese esquema. Revisar `isLinkedToUser()` e `isPendingLink()` para contemplar `null` y `0`. |
| **`withInput()` en errores** | En `store_existing_user` y `store_new_user` el `catch` hace `back()->withErrors(...)` sin `withInput()`. | **Aplicar:** Añadir `withInput()` en esas redirecciones de error y usar `old()` en las vistas de alta (p. ej. `add_information`) en los campos que se quieran repoblar. |

### 1.4. Validaciones avanzadas (frontend/backend)

| Punto del informe | Estado actual | Acción recomendada |
|-------------------|---------------|--------------------|
| **DNI/NIE (Módulo 23) y CIF en frontend** | No se ha comprobado validación JS en tiempo real en formularios de vendedores. | **Aplicar:** Añadir validación JS (Módulo 23, CIF) en los formularios de alta/edición de vendedores, coherente con `SpanishDocument`. |
| **Edad mínima 18 años** | Se usa `MinimumAge(18)` en backend (p. ej. en `SellerController`). | **Hecho.** |
| **Detección de duplicados por email (AJAX)** | Existe `check_user_email` que comprueba si el email existe en `users`. No hay modal para “Invitación” vs “Vendedor externo” en flujo de vendedores. | **Valorar:** Mantener el chequeo AJAX y, si el cliente lo confirma, añadir modal para elegir entre “Invitación” o “Vendedor externo” cuando se detecte duplicado. |

---

## 2. Módulo de usuarios

### 2.1. Interactividad y control (`UserController`, `users/index`)

| Punto del informe | Estado actual | Acción recomendada |
|-------------------|---------------|--------------------|
| **Filas clickables (`row-clickable`)** | La tabla de usuarios no tiene filas clickables que lleven a la ficha; se usa botón “Ver”. | **Aplicar:** Hacer las filas de la tabla clickables (p. ej. `data-id`), y que el click lleve a `users.show` (manteniendo el botón Ver si se desea). |
| **AJAX status toggle** | No hay ruta ni método para cambiar estado (Activo/Bloqueado) desde la ficha sin recargar. | **Aplicar:** Nueva ruta (p. ej. `POST users/{user}/toggle-status`) y método en `UserController` que cambie `status` y devuelva JSON; en `users/show` llamar por AJAX y actualizar badge/UI. |
| **Apertura por pestaña (Cartera/Historial)** | `UserController::show` no recibe parámetro de pestaña. La vista tiene wizard “Datos / Cartera / Historial” pero solo existe el pane `datos_usuario`. | **Aplicar (parcial):** Soporte de `?tab=cartera` y `?tab=historial` en `show`, pasar `$tab` a la vista y activar el elemento del sidebar correspondiente. Crear los panes “Cartera” e “Historial” cuando se definan contenidos. |

---

## 3. Módulo de entidades y administraciones

### 3.1. Selección y wizard de alta (`EntityController`, `AdministratorController`)

| Punto del informe | Estado actual | Acción recomendada |
|-------------------|---------------|--------------------|
| **Selección implícita (sin radios)** | En `entities/add` hay tabla con radios para elegir administración; ya existe click en fila que marca el radio. | **Opcional:** Eliminar radios visibles y usar solo `data-id` en `<tr>`, hidden input y JS que actualice el valor y envíe el form. Mejora UX, no bloqueante. |
| **Limpieza de prefijo IBAN “ES” duplicado** | En `AdministratorController` se hace `trim` y `str_replace` de espacios en `account`; luego se concatena `'ES' + account`. No se elimina un “ES” inicial si el usuario lo pega. | **Aplicar:** Antes de validar/guardar, eliminar prefijo “ES” (case-insensitive) del valor de cuenta si existe, y después construir el IBAN solo con dígitos + “ES” por delante. Revisar tanto `update` como `store_information` (y flujos que usen `account`). |
| **Validación reforzada en `store`** | El informe pide evitar `SQLSTATE[23000]` en campos obligatorios. | **Revisar:** Comprobar `EntityController::store_*` y `AdministratorController::store`; asegurar que se validan todos los campos obligatorios antes de `create`/`update` y que no se asignan `null` a columnas NOT NULL. |

---

## 4. Core y validación transversal

### 4.1. Reglas de validación (`app/Rules/`)

| Punto del informe | Estado actual | Acción recomendada |
|-------------------|---------------|--------------------|
| **`SpanishDocument`** | Existe y se usa en Administrations, SEPA, etc. Hace trim y valida DNI/NIE/CIF. | **Hecho.** |
| **Uso en formularios de alta** | No se usa en `UserController` (create/update), ni en `SellerController` (store), ni en `EntityController::store_information` para `nif_cif`. | **Aplicar:** Añadir `SpanishDocument` en validaciones de usuarios, vendedores y entidades (alta/edición) donde se valide `nif_cif`. |
| **`MinimumAge`** | Existe, parametrizable (18). Se usa en vendedores y en AdministratorController. | **Hecho.** |
| **User create/update** | `UserController` valida `nif_cif` con `required|string|max:20|unique:...` pero sin `SpanishDocument`. | **Aplicar:** Incluir `SpanishDocument` en `store` y `update` de `UserController` (y usar `old('nif_cif')` en las vistas si aún no se hace). |

### 4.2. Base de datos

| Punto del informe | Estado actual | Acción recomendada |
|-------------------|---------------|--------------------|
| **Campo `admin_number` en administraciones** | La migración de `administrations` no define `admin_number`. | **Aplicar:** Nueva migración que añada `admin_number` (string nullable o según especificación) a `administrations`. |
| **Scripts de mantenimiento / updates masivos** | No hay comandos ni migraciones para “corregir estados de vendedores antiguos” tras cambio de tipos. | **Valorar:** Si se cambia la semántica de `status` (0/1/2), crear comando Artisan o migración de datos que actualice registros antiguos según reglas acordadas con el cliente. |

---

## 5. Resumen de prioridades

### Prioridad alta (aplicar pronto)

1. **Seller:** quitar cast `boolean` de `status` y soportar 0/1/2; ajustar accessors y vistas.
2. **SellerController `update`:** solo sincronizar grupos cuando `group_id` esté presente en la petición.
3. **SellerService:** estados por defecto externos = 1, partilot pendientes = 2; y `withInput()` en redirecciones de error de store.
4. **Validación:** usar `SpanishDocument` en Users, Sellers y Entities (donde aplique `nif_cif`).
5. **`sellers/show` JS:** subir `participacionesAsignadas` (y variables relacionadas) a un scope superior y quitar el `let` duplicado para evitar TDZ y comportamientos raros.

### Prioridad media

6. **IBAN:** limpiar prefijo “ES” duplicado en `AdministratorController` (update y store).
7. **Usuarios:** filas clickables en índice, AJAX toggle de estado en ficha, y soporte de `?tab=` en `show`.
8. **Seller:** accessors `debt_amount` y totales de participaciones; y `old()` en formularios de alta cuando se añada `withInput()`.

### Prioridad baja / valorar con cliente

9. **Modo mode-data / mode-ops** en `sellers/show`, **modal Invitación vs Externo** en duplicados por email, **selección implícita sin radios** en entidades, **scripts de mantenimiento** de estados de vendedores.

---

## 6. Cambios ya implementados (esta sesión)

- **Seller:** Cast `status` eliminado; constantes 0/1/2; accessors `status_text`/`status_class` con `match()`; validación `status` como `integer|in:0,1,2`.
- **SellerController update:** Grupos solo se sincronizan/desvinculan cuando `group_id` está presente en la petición.
- **SellerService:** Externos por defecto `STATUS_ACTIVE` (1); Partilot pendientes `STATUS_PENDING` (2); `withInput()` en redirecciones de error de store.
- **Validación:** Regla `SpanishDocument` en UserController (store/update), SellerController (store/update y alta), EntityController (store_information).
- **sellers/show JS:** Variables `participacionesAsignadas` y `participacionesDisponibles` declaradas al inicio del bloque de asignación; eliminado el `let` duplicado.
- **AdministratorController:** Helper `sanitizeIbanAccount()` para quitar espacios, prefijo "ES" duplicado y dejar solo dígitos; usado en update y store_information.
- **Usuarios:** Filas clickables en índice (`data-href`, script para navegar al hacer clic); ruta y método `toggleStatus` para cambiar estado por AJAX; `show` acepta `?tab=` y pasa `$tab` a la vista; badge y botón "Cambiar estado" en ficha con actualización por AJAX.
- **Ruta users:** GET `users` pasa a usar `UserController::index` para enviar `$users` a la vista.

---

## 7. Nota sobre compatibilidad

El informe indica que los cambios en controladores mantienen compatibilidad hacia atrás con vistas anteriores. Al aplicar los puntos anteriores, conviene:

- No eliminar parámetros ni rutas usadas por otras vistas o integraciones.
- Probar listados y fichas de vendedores, usuarios y entidades después de cada cambio.
- Usar las vistas Blade actualizadas que mentiona el informe (“Apple Style”) donde existan, para beneficiarse de AJAX y modales.

Si quieres, el siguiente paso puede ser implementar solo los puntos de **prioridad alta** en el código y dejar preparadas las migraciones y rutas necesarias para el resto.
