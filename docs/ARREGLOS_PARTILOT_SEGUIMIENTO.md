# Seguimiento arreglos Partilot (PDF + acuerdos)

Leyenda: `[ ]` pendiente · `[~]` en curso / parcial · `[x]` hecho · `[—]` descartado / para más adelante

**Última actualización:** 2026-06-03

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
| [ ] | Modales 2 pasos al cerrar sorteo + mails compradores | Pendiente |
| [~] | Admin: solo escrutinio; flujo simplificado | Solo «Lista Resultados» (sin botón Escrutinio duplicado) |
| [~] | Restricción vistas sorteos por rol | `LotteryPanelAccess` + `lottery/index`; falta `show`/`edit`/middleware |

---

## Global / otros (para más adelante)

| Estado | Tarea | Notas |
|--------|-------|-------|
| [—] | DataTables en castellano | Revisar después |
| [—] | Dashboard cuadrados acceso rápido | Feature nueva |
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
- `routes/web.php` — `sellers.update-comment`

---

## Registro de pruebas manuales

| Fecha | Quién | Área | Resultado |
|-------|-------|------|-----------|
| | | Gestores: invitar / lista / cambiar responsable | |
| | | Entidad: sin editar / sin comentarios admin | |
| | | Admin: sin pestaña gestores / sin col. administración | |
| | | Vendedor: grupo / observaciones / admin sin datos personales | |
| | | Sorteos: listado por rol | |

correr comando en servidor
* * * * * cd /ruta/al/proyecto/sipart && php artisan schedule:run >> /dev/null 2>&1
