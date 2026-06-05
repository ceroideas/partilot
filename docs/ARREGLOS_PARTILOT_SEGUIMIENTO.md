# Seguimiento arreglos Partilot (PDF + acuerdos)

Leyenda: `[ ]` pendiente · `[~]` en curso / parcial · `[x]` hecho · `[—]` descartado / para más adelante

**Última actualización:** 2026-06-04

---

## Rol entidad

| Estado | Tarea | Notas / prueba |
|--------|-------|----------------|
| [x] | No editar datos entidad ni datos gestor | Botones ocultos; rutas `edit`/`update`/`edit-manager` con 403 para entidad |
| [x] | Sin campo observaciones de administración | Comentarios entidad/gestor solo si `canSeeAdminComments` |
| [x] | Etiqueta «Gestor responsable» en listado | Badge + pestaña lateral renombrada |
| [~] | Icono cambiar gestor responsable (captura 1) | Botón azul circular; 1er clic muestra selector, 2º confirma — **probar flujo** |
| [x] | Solo invitar gestor (no registrar) para rol entidad / gestor responsable | `#register-manager` oculto si `hideRegisterManager` |

## Rol administración (entidades)

| Estado | Tarea | Notas / prueba |
|--------|-------|----------------|
| [x] | Sin pestaña Gestores en ficha entidad | `hideGestoresTab` para administración |
| [x] | Sin alta de gestor desde gestores (admin) | `canManageSecondaryManagers` ya no incluye administración |
| [x] | Sin columna Administración en listado principal entidades | También para rol administración |

## Vendedores

| Estado | Tarea | Notas / prueba |
|--------|-------|----------------|
| [x] | Ocultar banner entidad (rol entidad) | Sidebar + bloque en pestaña datos |
| [x] | No editar ficha vendedor (salvo observaciones entidad) | Editar solo superadmin; observaciones vía `sellers.update-comment` |
| [x] | Mostrar grupo en ficha sin entrar a editar | Campo Grupo en show |
| [x] | Venta digital sin asignación física al aceptar invitación | **Verificado en código/docs** — pool digital común (`docs/devoluciones-participaciones-digitales.md`) |
| [x] | Admin: sin datos personales en ficha vendedor | Sin pestaña Dat. Vendedor, sin tarjeta nombre/email lateral |

## Sorteos

| Estado | Tarea | Notas / prueba |
|--------|-------|----------------|
| [~] | Entidad: solo ver premio/desglose propio | Listado restringido; falta vista `show` solo premios |
| [~] | Admin: solo editar fecha/hora límite (tope Partilot) | Botón editar admin en índice; falta formulario acotado + validación tope |
| [x] | Avisos automáticos 3/2/1/0 días (modal + email) | `LotteryDeadlineReminderService`, cron 09:00, modal en layout, log anti-duplicados |
| [x] | Cierre: no devueltas → vendidas + deuda | `LotteryDeadlineClosureService`, cron 00:30 si `LOTTERY_AUTO_DEADLINE_CLOSURE_ENABLED=true` |
| [—] | Modales 2 pasos al cerrar sorteo + mails compradores | Pendiente confirmación cliente (diseños en capturas) |
| [~] | Admin: solo escrutinio; flujo simplificado | Solo «Lista Resultados» (sin botón Escrutinio duplicado) |
| [~] | Restricción vistas sorteos por rol | `LotteryPanelAccess` + `lottery/index`; falta `show`/`edit`/middleware |

---

## Gestor multi-entidad (contexto activo)

| Estado | Tarea | Notas |
|--------|-------|-------|
| [x] | Selector entidad activa (sesión) + menú por permisos | `ActiveEntityContext`, desplegable topbar, `panel.switch-entity` |

### Spec acordada — entidad activa en sesión

**Alcance:** gestores con 2..N entidades (`managers.entity_id`). No aplica a cuenta panel `panel_account_type=entity` (una entidad fija).

**UI**
- Desplegable **permanente** en cabecera (y/o tarjetas en dashboard al entrar si aún no hay selección).
- Muestra nombre entidad + rol: **Gestor responsable** o **Gestor** (permisos parciales según `Manager` de esa entidad).
- Al cambiar entidad: **reset de pantalla** → redirigir a dashboard (o home del panel).

**Persistencia**
- Solo **sesión** (`active_entity_id`), no tabla BD.
- **Nuevo login:** no recordar selección anterior; volver a elegir / auto-asignar.

**Auto-selección al login (prioridad)**
1. Si es gestor **principal** (`is_primary`) de una o más entidades → primera entidad principal (orden estable, p. ej. `managers.id` o nombre).
2. Si **solo** es gestor secundario en todas → primera entidad gestionada (misma orden).
3. El usuario puede cambiar después al resto (secundarias u otras principales si tiene varias).

**Menú y datos**
- Vendedores, diseño, pagos, etc. visibles según permisos del `Manager` **de la entidad activa** (no OR global entre entidades).
- Listados y flujos (devoluciones, reservas, sets…) filtrados por entidad activa; sin paso extra de elegir entidad cuando hay contexto.

**Piezas técnicas previstas**
- Extender `PanelSelectionResolver` / `ActiveEntityContext` + middleware.
- `POST panel/switch-entity` → sesión + redirect dashboard.
- Ajustar `hasEntityManagerPermission()`, `layout.blade.php` (menú), controladores que usan `accessibleEntityIds()`.

---

## Global / otros (para más adelante)

| Estado | Tarea | Notas |
|--------|-------|-------|
| [—] | DataTables en castellano | Revisar después |
| [—] | Dashboard cuadrados acceso rápido | Puede integrarse con selector multi-entidad |
| [—] | Sección Administración en panel | Feature nueva |
| [—] | Flujo invitación gestor/vendedor ampliado (aceptar/rechazar, condiciones) | Feature grande |
| [—] | Icono cerrar sesión junto a notificaciones (Figma) | UI menor |

---

## Archivos clave tocados (2026-06-03)

- `app/Http/Controllers/EntityController.php`
- `app/Http/Controllers/SellerController.php`
- `app/Http/Controllers/LotteryController.php`
- `app/Support/LotteryPanelAccess.php`
- `resources/views/entities/show.blade.php`
- `resources/views/sellers/show.blade.php`
- `resources/views/lottery/index.blade.php`
- `routes/web.php` — `sellers.update-comment`, `panel.switch-entity`
- `app/Support/ActiveEntityContext.php` — gestor multi-entidad

---

## Registro de pruebas manuales

| Fecha | Quién | Área | Resultado |
|-------|-------|------|-----------|
| | | Gestores: invitar / lista / cambiar responsable | |
| | | Entidad: sin editar / sin comentarios admin | |
| | | Admin: sin pestaña gestores / sin col. administración | |
| | | Vendedor: grupo / observaciones / admin sin datos personales | |
| | | Sorteos: listado por rol | |
| | | Gestor multi-entidad: selector / menú por permisos | |
