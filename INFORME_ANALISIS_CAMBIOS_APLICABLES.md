# 📋 Análisis de Cambios Aplicables
## Informe Técnico del Cliente

---

**📅 Fecha:** 29 de enero de 2026  
**🎯 Alcance:** Comparación del informe técnico (22 ene 2026) con el estado actual del sistema (sipart)

---

## 📌 Resumen Ejecutivo

Este documento analiza qué puntos del informe técnico:
- ✅ **Ya están implementados**
- ⚠️ **Faltan o difieren del informe**
- 🔧 **Se pueden aplicar** en el código actual

---

## 1️⃣ Módulo de Vendedores (Core)

### 1.1. Estabilización de Ficha de Vendedor
**Archivo:** `sellers/show.blade.php`

| Punto del Informe | Estado Actual | Acción Recomendada |
|-------------------|---------------|-------------------|
| **🔴 Scope JS / Variables** | Hay uso de `participacionesAsignadas` sin declaración global y `let participacionesAsignadas` dentro de un handler (aprox. líneas 1305, 1351, 1369). **Riesgo de TDZ y duplicación de variable.** | **🔧 APLICAR:**<br>• Declarar `participacionesAsignadas` y `participacionesDisponibles` en scope superior (al inicio del bloque de asignación)<br>• Eliminar el `let` duplicado dentro del handler |
| **🟡 Máquina de Estados UI** | No existe lógica explícita `mode-data` vs `mode-ops` en el sidebar para alternar entre "gestión/bloqueo" y "contexto usuario/entidad" según pestaña. | **💭 VALORAR:**<br>• Revisar con el cliente si tienen mockups o flujo exacto<br>• Si no, dejar para segunda fase y documentar como mejora futura |
| **🟡 Edición Inline (readonly toggle)** | No se ha revisado en detalle el script de desbloqueo de inputs ni posibles eventos jQuery mal anidados en `sellers/show`. | **🔍 REVISAR:**<br>• Comprobar en `sellers/edit` y `sellers/show` si hay edición inline<br>• Si existe, unificar lógica y asegurar que no haya listeners duplicados o mal anidados |

---

### 1.2. Lógica de Negocio y Modelado
**Archivos:** `Seller.php`, `SellerController.php`

| Punto del Informe | Estado Actual | Acción Recomendada |
|-------------------|---------------|-------------------|
| **🔴 Sincronización de Grupos** | En `update` se hace `sync` si `group_id` existe y no está vacío; si no, se hace `detach`. En ediciones parciales (sin `group_id`) se desvincula el grupo. | **🔧 APLICAR:**<br>• Solo tocar grupos cuando `group_id` esté presente en la petición<br>• Si no viene `group_id`, **no modificar** `groups()`<br>• Si viene y está vacío, se puede hacer `detach` (elección de negocio) |
| **✅ Propagación a `User`** | Al actualizar vendedor ya se actualiza el `User` vinculado (nombre, email, teléfono, etc.) cuando `user_id` existe. | **✅ HECHO** |
| **🔴 Cast de `status`** | En `Seller` hay `'status' => 'boolean'`. El informe pide estados multivalor (0: Inactivo, 1: Activo, 2: Pendiente, etc.). | **🔧 APLICAR:**<br>• Quitar el cast `'status' => 'boolean'` en `Seller`<br>• Adaptar accesores `status_text` / `status_class` (y vistas que usen `status`) para soportar 0, 1, 2 |
| **🟡 Accessors de Deuda y Totales** | No hay atributos virtuales `debt_amount` ni totales de participaciones en el modelo. | **🔧 APLICAR:**<br>• Añadir en `Seller` accessors (o atributos calculados) para `debt_amount` y totales de participaciones según reglas de negocio acordadas (liquidaciones, participaciones asignadas/vendidas, etc.) |

---

### 1.3. Alta y Persistencia
**Archivos:** `SellerService.php`, migraciones

| Punto del Informe | Estado Actual | Acción Recomendada |
|-------------------|---------------|-------------------|
| **✅ `user_id` nullable en `sellers`** | Migraciones ya tienen `user_id` nullable en `sellers`. | **✅ HECHO** |
| **🔴 Estados por Defecto** | • Externos: `status` por defecto viene de `$data['status'] ?? false` (0)<br>• Partilot pendientes: `status => false` (0)<br>• **Informe requiere:** externos ACTIVOS (1), online PENDIENTES (2) | **🔧 APLICAR:**<br>• En `SellerService`: externos por defecto `status = 1`<br>• Partilot pendientes `status = 2`<br>• Ajustar `createPartilotSeller` / `createExternalSeller` según corresponda |
| **🟡 `user_id` para Externos** | Se usa `user_id => 0` para externos y pendientes. Informe indica "vendedores offline" sin cuenta → `user_id` NULL. | **💭 VALORAR:**<br>• Usar `user_id => null` para externos<br>• Mantener `0` para "pendientes de vincular" si se sigue ese esquema<br>• Revisar `isLinkedToUser()` e `isPendingLink()` para contemplar `null` y `0` |
| **🔴 `withInput()` en Errores** | En `store_existing_user` y `store_new_user` el `catch` hace `back()->withErrors(...)` sin `withInput()`. | **🔧 APLICAR:**<br>• Añadir `withInput()` en esas redirecciones de error<br>• Usar `old()` en las vistas de alta (p. ej. `add_information`) en los campos que se quieran repoblar |

---

### 1.4. Validaciones Avanzadas
**Alcance:** Frontend/Backend

| Punto del Informe | Estado Actual | Acción Recomendada |
|-------------------|---------------|-------------------|
| **🟡 DNI/NIE (Módulo 23) y CIF en Frontend** | No se ha comprobado validación JS en tiempo real en formularios de vendedores. | **🔧 APLICAR:**<br>• Añadir validación JS (Módulo 23, CIF) en los formularios de alta/edición de vendedores<br>• Coherente con `SpanishDocument` |
| **✅ Edad Mínima 18 Años** | Se usa `MinimumAge(18)` en backend (p. ej. en `SellerController`). | **✅ HECHO** |
| **🟡 Detección de Duplicados por Email (AJAX)** | Existe `check_user_email` que comprueba si el email existe en `users`. No hay modal para "Invitación" vs "Vendedor externo" en flujo de vendedores. | **💭 VALORAR:**<br>• Mantener el chequeo AJAX<br>• Si el cliente lo confirma, añadir modal para elegir entre "Invitación" o "Vendedor externo" cuando se detecte duplicado |

---

## 2️⃣ Módulo de Usuarios

### 2.1. Interactividad y Control
**Archivos:** `UserController`, `users/index`

| Punto del Informe | Estado Actual | Acción Recomendada |
|-------------------|---------------|-------------------|
| **🟡 Filas Clickables (`row-clickable`)** | La tabla de usuarios no tiene filas clickables que lleven a la ficha; se usa botón "Ver". | **🔧 APLICAR:**<br>• Hacer las filas de la tabla clickables (p. ej. `data-id`)<br>• El click debe llevar a `users.show` (manteniendo el botón Ver si se desea) |
| **🟡 AJAX Status Toggle** | No hay ruta ni método para cambiar estado (Activo/Bloqueado) desde la ficha sin recargar. | **🔧 APLICAR:**<br>• Nueva ruta (p. ej. `POST users/{user}/toggle-status`)<br>• Método en `UserController` que cambie `status` y devuelva JSON<br>• En `users/show` llamar por AJAX y actualizar badge/UI |
| **🟡 Apertura por Pestaña (Cartera/Historial)** | `UserController::show` no recibe parámetro de pestaña. La vista tiene wizard "Datos / Cartera / Historial" pero solo existe el pane `datos_usuario`. | **🔧 APLICAR (parcial):**<br>• Soporte de `?tab=cartera` y `?tab=historial` en `show`<br>• Pasar `$tab` a la vista y activar el elemento del sidebar correspondiente<br>• Crear los panes "Cartera" e "Historial" cuando se definan contenidos |

---

## 3️⃣ Módulo de Entidades y Administraciones

### 3.1. Selección y Wizard de Alta
**Archivos:** `EntityController`, `AdministratorController`

| Punto del Informe | Estado Actual | Acción Recomendada |
|-------------------|---------------|-------------------|
| **💭 Selección Implícita (sin radios)** | En `entities/add` hay tabla con radios para elegir administración; ya existe click en fila que marca el radio. | **💭 OPCIONAL:**<br>• Eliminar radios visibles y usar solo `data-id` en `<tr>`<br>• Hidden input y JS que actualice el valor y envíe el form<br>• Mejora UX, no bloqueante |
| **🔴 Limpieza de Prefijo IBAN "ES" Duplicado** | En `AdministratorController` se hace `trim` y `str_replace` de espacios en `account`; luego se concatena `'ES' + account`. **No se elimina un "ES" inicial si el usuario lo pega.** | **🔧 APLICAR:**<br>• Antes de validar/guardar, eliminar prefijo "ES" (case-insensitive) del valor de cuenta si existe<br>• Después construir el IBAN solo con dígitos + "ES" por delante<br>• Revisar tanto `update` como `store_information` (y flujos que usen `account`) |
| **🟡 Validación Reforzada en `store`** | El informe pide evitar `SQLSTATE[23000]` en campos obligatorios. | **🔍 REVISAR:**<br>• Comprobar `EntityController::store_*` y `AdministratorController::store`<br>• Asegurar que se validan todos los campos obligatorios antes de `create`/`update`<br>• No asignar `null` a columnas NOT NULL |

---

## 4️⃣ Core y Validación Transversal

### 4.1. Reglas de Validación
**Directorio:** `app/Rules/`

| Punto del Informe | Estado Actual | Acción Recomendada |
|-------------------|---------------|-------------------|
| **✅ `SpanishDocument`** | Existe y se usa en Administrations, SEPA, etc. Hace trim y valida DNI/NIE/CIF. | **✅ HECHO** |
| **🔴 Uso en Formularios de Alta** | No se usa en `UserController` (create/update), ni en `SellerController` (store), ni en `EntityController::store_information` para `nif_cif`. | **🔧 APLICAR:**<br>• Añadir `SpanishDocument` en validaciones de usuarios, vendedores y entidades (alta/edición) donde se valide `nif_cif` |
| **✅ `MinimumAge`** | Existe, parametrizable (18). Se usa en vendedores y en AdministratorController. | **✅ HECHO** |
| **🔴 User create/update** | `UserController` valida `nif_cif` con `required|string|max:20|unique:...` pero sin `SpanishDocument`. | **🔧 APLICAR:**<br>• Incluir `SpanishDocument` en `store` y `update` de `UserController`<br>• Usar `old('nif_cif')` en las vistas si aún no se hace |

---

### 4.2. Base de Datos

| Punto del Informe | Estado Actual | Acción Recomendada |
|-------------------|---------------|-------------------|
| **🟡 Campo `admin_number` en Administraciones** | La migración de `administrations` no define `admin_number`. | **🔧 APLICAR:**<br>• Nueva migración que añada `admin_number` (string nullable o según especificación) a `administrations` |
| **💭 Scripts de Mantenimiento / Updates Masivos** | No hay comandos ni migraciones para "corregir estados de vendedores antiguos" tras cambio de tipos. | **💭 VALORAR:**<br>• Si se cambia la semántica de `status` (0/1/2), crear comando Artisan o migración de datos<br>• Actualizar registros antiguos según reglas acordadas con el cliente |

---

## 5️⃣ Resumen de Prioridades

### 🔴 Prioridad Alta (Aplicar Pronto)

1. **Seller - Cast de `status`:** Quitar cast `boolean` de `status` y soportar 0/1/2; ajustar accessors y vistas.
2. **SellerController `update` - Grupos:** Solo sincronizar grupos cuando `group_id` esté presente en la petición.
3. **SellerService - Estados y Errores:** 
   - Externos por defecto `status = 1`
   - Partilot pendientes `status = 2`
   - Añadir `withInput()` en redirecciones de error de store
4. **Validación - `SpanishDocument`:** Usar en Users, Sellers y Entities (donde aplique `nif_cif`).
5. **`sellers/show` JS - Variables:** Subir `participacionesAsignadas` (y variables relacionadas) a un scope superior y quitar el `let` duplicado para evitar TDZ y comportamientos raros.

---

### 🟡 Prioridad Media

6. **IBAN - Prefijo Duplicado:** Limpiar prefijo "ES" duplicado en `AdministratorController` (update y store).
7. **Usuarios - Interactividad:** 
   - Filas clickables en índice
   - AJAX toggle de estado en ficha
   - Soporte de `?tab=` en `show`
8. **Seller - Accessors:** `debt_amount` y totales de participaciones; y `old()` en formularios de alta cuando se añada `withInput()`.

---

### 💭 Prioridad Baja / Valorar con Cliente

9. **Mejoras Futuras:**
   - Modo `mode-data` / `mode-ops` en `sellers/show`
   - Modal "Invitación vs Externo" en duplicados por email
   - Selección implícita sin radios en entidades
   - Scripts de mantenimiento de estados de vendedores

---

## 6️⃣ Cambios Ya Implementados (Esta Sesión)

✅ **Seller:**
- Cast `status` eliminado
- Constantes 0/1/2 definidas
- Accessors `status_text`/`status_class` con `match()`
- Validación `status` como `integer|in:0,1,2`

✅ **SellerController update:**
- Grupos solo se sincronizan/desvinculan cuando `group_id` está presente en la petición

✅ **SellerService:**
- Externos por defecto `STATUS_ACTIVE` (1)
- Partilot pendientes `STATUS_PENDING` (2)
- `withInput()` en redirecciones de error de store

✅ **Validación:**
- Regla `SpanishDocument` en:
  - `UserController` (store/update)
  - `SellerController` (store/update y alta)
  - `EntityController` (store_information)

✅ **sellers/show JS:**
- Variables `participacionesAsignadas` y `participacionesDisponibles` declaradas al inicio del bloque de asignación
- Eliminado el `let` duplicado

✅ **AdministratorController:**
- Helper `sanitizeIbanAccount()` para quitar espacios, prefijo "ES" duplicado y dejar solo dígitos
- Usado en `update` y `store_information`

✅ **Usuarios:**
- Filas clickables en índice (`data-href`, script para navegar al hacer clic)
- Ruta y método `toggleStatus` para cambiar estado por AJAX
- `show` acepta `?tab=` y pasa `$tab` a la vista
- Badge y botón "Cambiar estado" en ficha con actualización por AJAX

✅ **Ruta users:**
- GET `users` pasa a usar `UserController::index` para enviar `$users` a la vista

---

## 7️⃣ Nota sobre Compatibilidad

El informe indica que los cambios en controladores mantienen **compatibilidad hacia atrás** con vistas anteriores. Al aplicar los puntos anteriores, conviene:

⚠️ **Precauciones:**
- No eliminar parámetros ni rutas usadas por otras vistas o integraciones
- Probar listados y fichas de vendedores, usuarios y entidades después de cada cambio
- Usar las vistas Blade actualizadas que menciona el informe ("Apple Style") donde existan, para beneficiarse de AJAX y modales

---

## 📝 Próximos Pasos Sugeridos

Si quieres, el siguiente paso puede ser implementar solo los puntos de **prioridad alta** en el código y dejar preparadas las migraciones y rutas necesarias para el resto.

---

**Leyenda de Iconos:**
- ✅ **Hecho** - Ya implementado
- 🔧 **Aplicar** - Se debe implementar
- 🟡 **Revisar/Valorar** - Requiere análisis o decisión
- 🔴 **Prioridad Alta** - Crítico
- 💭 **Opcional** - Mejora futura
