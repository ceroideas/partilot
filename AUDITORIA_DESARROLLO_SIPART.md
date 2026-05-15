# Auditoría de desarrollo SIPART / PARTILOT

**Fecha:** 15 de mayo de 2026  
**Alcance:** Panel Laravel (`h:/xampp3/htdocs/sipart`) + App Ionic (`h:/Users/Jorge/proyectos/sipart`)  
**Referencias:** `especificacion.md`, informes y listas de tareas en `.md` del repositorio

---

## 1. Resumen ejecutivo

| Área | Estado global | Comentario |
|------|---------------|------------|
| **Core panel** (admin, entidades, reservas, sets, vendedores) | **~75–85 %** | Flujos principales operativos; incidencias puntuales de UI e imágenes |
| **Diseño e impresión** | **~60–70 %** | Editor y PDFs base; pendientes de salida, XML, digitales, imprenta |
| **Sorteos y resultados** | **~80 %** | Alta, API resultados, escrutinio; publicación manual desde panel |
| **App móvil (Ionic)** | **~70–75 %** | Venta, cartera, gestor, notificaciones API; aceptación invitaciones pendiente |
| **Notificaciones push / inbox** | **~85 %** | API bandeja, eventos enlazados, FCM con `notification_id`; cron automático **no** |
| **Web venta / TPV / iframes** | **~30–40 %** | `SocialWebController` parcial; flujo spec completo no cerrado |
| **Magic links admin** | **~0–20 %** | Documentado en `nuevo desarrollo magic links.md`; no implementado como spec |
| **Cron / tareas programadas** | **0 % activo** | `Kernel::schedule()` vacío; comandos `sipart:*` en stub |

**Para cerrar el desarrollo** quedan sobre todo: diseño/impresión (salida, XML, digitales), integraciones (web/TPV, imprenta, magic links), robustez de pagos, y pulido app + panel.

---

## 2. Documentación del proyecto (inventario)

### 2.1 Documento inicial

| Archivo | Rol |
|---------|-----|
| **`especificacion.md`** | Especificación funcional original (procesos panel: altas, reservas, sets, venta, asignación, cobro, diseño, imprimir, sorteos, devoluciones, web/iframe). Muchos procesos marcados **DISEÑADO** en texto; no implica implementación completa. |

### 2.2 Listas de tareas e informes (raíz)

| Archivo | Contenido | Tratamiento en esta auditoría |
|---------|-----------|-------------------------------|
| `tareas nuevas.md` | Incidencias diseño/UI (imágenes, zoom, PDF, digitales) | Pendientes → `docs/PENDIENTES_DESARROLLO.md`; archivo sustituido por puntero |
| `nuevas tareas 16-02.md` | 25 tareas detalladas (gestores, diseño, XML, app) | Completadas archivadas; pendientes en `PENDIENTES` |
| `Tareas a realizar.md` | 5 bloques (imágenes admin/entidad, CIF G, fecha límite, Fademur) | **Todo marcado ✅** → histórico en `TAREAS_REALIZADAS.md` |
| `TAREAS_REALIZADAS.md` | Resumen incidencias resueltas (IBAN, imágenes, sorteos, sets…) | **Histórico** — mantener |
| `ANALISIS_MEJORAS_TAREAS_NUEVAS.md` | Análisis técnico de `tareas nuevas.md` | Referencia; duplica pendientes de diseño |
| `INFORME_ANALISIS_CAMBIOS_APLICABLES.md` | Informe cliente 22-ene-2026 vs código | Muchos ítems ✅; varios 🔧 siguen abiertos (ver §5) |
| `RESPUESTA_INFORME_AUDITORIA.md` | Respuesta auditoría técnica (vendedor bloqueado, user_id=0…) | 3.2 validación vendedor **pendiente verificar** en código |
| `mejoras.md` | UX rutas concretas (imágenes entidad, atrás sets, performance asignación…) | Pendientes UX → `PENDIENTES` |
| `mejoras.md` / `FIX_*` / `CORRECCIONES_*` | Fixes puntuales app | Revisar caso a caso; varios ya aplicados en git |
| `tacos.md` | Venta por taco + digital en app | Parcialmente implementado; venta digital email pendiente |
| `cambios dedign.md` / `cambio en asignacion-devolucion.md` | Cambios diseño y devolución | Pendientes de diseño externo / flujos |
| `PRESUPUESTO_ALTA_ADMIN_ENTIDADES.md` | Gap magic link + aceptación gestor | **Pendiente funcional** |
| `nuevo desarrollo magic links.md` | Spec magic link superadmin | **No implementado** |
| `Programa impresion.md` | Programa impresión | Ver `docs/impresion*.md` |
| `puntos audios.md` | Audios | Fuera de alcance código actual |

### 2.3 Carpeta `docs/`

| Archivo | Rol |
|---------|-----|
| `especificacion-vs-desarrollo.md` | **Gap analysis** spec vs código (plantillas, web/TPV, imprimir, firmas…) |
| `resumen_estado_impresion_diseno.md` | Núcleo diseño hecho; pendientes operativos/pagos/webhooks |
| `plan_operativo_impresion.md` / `plan_colas_background.md` | Planes de cierre módulo impresión/colas |
| `impresion.md` / `impresion_original.md` | Especificación impresión |
| `devoluciones-flujo-para-app.md` | API devoluciones para Ionic — **documentado** |
| `devoluciones-participaciones-digitales.md` | Devolución venta digital app — **pendiente** |
| `cuentas-panel-administracion-entidad.md` | Cuentas panel |
| `SCHEDULER_AND_KIND_LABELS.md` | Cron stub + kinds notificaciones |

### 2.4 Firebase / notificaciones (histórico)

`FIREBASE_*.md`, `SOLUCION_*.md`, `PRUEBA_*.md`, `NOTIFICACIONES_*.md` — configuración y pruebas ya realizadas. **No son backlog de producto**; conservar como referencia técnica o mover a `docs/_archivo/`.

### 2.5 App Ionic

| Archivo | Rol |
|---------|-----|
| `API_CONEXION_README.md` / `API_IMPLEMENTATION_GUIDE.md` | Conexión API |
| `correcciones qr.md` | Correcciones QR |

---

## 3. Comparativa: especificación inicial vs desarrollo actual

Leyenda: **✅** implementado (usable) · **⚠️** parcial · **❌** no / stub · **🔧** manual (sin cron)

| Proceso (`especificacion.md`) | Estado | Notas |
|------------------------------|--------|--------|
| Alta administración | ✅ | Panel + emails; **magic link spec nueva no aplicada** |
| Alta entidad + gestor | ⚠️ | Flujo base OK; aceptación/denegación gestor responsable incompleta |
| Reserva lotería | ✅ | Validaciones importe/redondeo mejoradas |
| Creación set participaciones | ✅ | Límites por reserva; incidencias imagen entidad en listados |
| Registro vendedor PARTILOT/externo | ✅ | Confirmación email PARTILOT; externos activos sin confirmación |
| Venta participaciones (panel) | ✅ | |
| Asignación tacos → vendedor | ⚠️ | Funcional; performance `save-assignments`; firma offline **no** |
| Cobro participaciones (panel) | ✅ | |
| Diseño participaciones | ⚠️ | Editor operativo; plantillas SIPART/TIKET/ASG **no**; fondo/XML/salida con gaps |
| Exportación participaciones | ⚠️ | PDF participación/portada/trasera en progreso; XML con errores conocidos |
| Web de venta + revisión admin | ⚠️ | `SocialWebController`; flujo TPV→iframe→email **no cerrado** |
| Iframes pago básico/completo | ⚠️ | Parcial / por contrastar |
| Enviar a diseñar (invitación 4 sem.) | ⚠️ | Diseño externo por token en parte; revocación/expiración 4 sem. **no verificado** |
| Enviar a imprimir (imprenta, tacos, pago) | ❌/⚠️ | Estados en docs; flujo completo spec **no** |
| Configuración sorteos / tipos | ✅ | |
| Asignación premios / resultados | ⚠️ | API + panel; notificación usuarios vía job al publicar; **no** cron “sorteo nuevo” |
| Devoluciones (panel + transmisión) | ⚠️ | Panel web; API app documentada; digitales y rectificación parcial |
| App: venta, QR, cartera, gestor | ⚠️ | Core OK; tacos/digital/email pendiente según `tacos.md` |
| App: notificaciones | ✅ | API inbox + push `notification_id`; etiquetas `kind` preparadas en app |
| Cron: pagos pendientes, recordatorios | ❌ | Solo stubs `sipart:*` + schedule comentado |

Detalle de gaps de producto: **`docs/especificacion-vs-desarrollo.md`**.

---

## 4. Tareas por estado (consolidado)

### 4.1 Realizadas (referencia — no reabrir salvo regresión)

**Fuente:** `TAREAS_REALIZADAS.md`, `Tareas a realizar.md` (todo ✅), `nuevas tareas 16-02.md` (tareas 1, 2, 4–6, 8–9, 11, 19–25 ✅), correcciones IBAN/CIF/fecha límite/Fademur, Firebase operativo, muchos puntos del informe 22-ene (toggle estado, SpanishDocument parcial, etc.).

**Notificaciones recientes (mayo 2026):**
- Migración inbox (`recipient_user_id`, `kind`, `meta`)
- API app `/api/notifications/*`
- Eventos: regalo, cobro, invitación vendedor, resultados (job), asignación (job)
- FCM `inbox_notification` + `notification_id`
- Panel: push manual con fila en bandeja
- App: listado, detalle, modal push, mapa `notification-kind.labels.ts` (UI etiquetas lista para pulir)

### 4.2 Pendientes — prioridad alta

| ID | Tema | Origen | Acción |
|----|------|--------|--------|
| P-H1 | **Configurar salida / ver y descargar PDFs** (participación, portada, trasera) | `tareas nuevas.md`, 16-02 T10–12 | Cerrar flujo post-`configure output` + enlaces en participaciones |
| P-H2 | **Imagen de fondo y agregar imágenes** en editor | `tareas nuevas.md`, 16-02 T3, T5 | Revisar upload + persistencia + URL en PDF |
| P-H3 | **Icono imprimir / error pantalla** | `tareas nuevas.md` | Logs DomPDF; async jobs |
| P-H4 | **XML sets** (REF vs 21 dígitos, digitales únicos, urlweb, importe por número, floor reserva) | 16-02 T14–18 | `SetController::downloadXml` |
| P-H5 | **Participaciones digitales: imagen + asignación + vínculo cliente** | 16-02 T13, `tacos.md` | Modelo + generación snapshot + flujo venta digital |
| P-H6 | **Magic links administración** (envío manual superadmin, sin auto-envío) | `nuevo desarrollo magic links.md`, presupuesto | Nuevo flujo auth |
| P-H7 | **Gestor responsable: aceptar/denegar y bloqueo entidad** | `especificacion.md`, presupuesto | API + app + emails |
| P-H8 | **Validación vendedor bloqueado en asignación** | `RESPUESTA_INFORME_AUDITORIA.md` §3.2 | Verificar/implementar en `saveAssignments` |

### 4.3 Pendientes — prioridad media

| ID | Tema | Origen |
|----|------|--------|
| P-M1 | Imágenes administración/entidad en wizards (alta gestor, sets, devoluciones, sellers) | `tareas nuevas.md`, `mejoras.md`, 16-02 |
| P-M2 | Zoom >150% con scroll; bordes selección textos | `tareas nuevas.md` |
| P-M3 | QR en editor portada (asignar tacos) | 16-02 T7 parte 2 |
| P-M4 | Cantidad total participaciones (default 600) | `tareas nuevas.md` |
| P-M5 | Plantillas diseño SIPART / TIKET / ASG + reverso | `especificacion-vs-desarrollo` |
| P-M6 | Web venta / TPV / iframes según spec | `especificacion-vs-desarrollo` |
| P-M7 | Flujo “Enviar a imprimir” completo (imprenta, estados, bloqueo edición) | spec + `plan_operativo_impresion.md` |
| P-M8 | INFORME 22-ene: cast `status` seller, sync grupos, SpanishDocument en todos los forms, IBAN ES duplicado | `INFORME_ANALISIS_CAMBIOS_APLICABLES.md` |
| P-M9 | Performance `save-assignments` | `mejoras.md` |
| P-M10 | Aceptar invitación vendedor en app (toast “próximamente”) | app `notificaciones.page.ts` |
| P-M11 | Etiquetas `kind` en UI listado (constante creada; falta pulir modal/detalle) | `SCHEDULER_AND_KIND_LABELS.md` |

### 4.4 Pendientes — prioridad baja / fase 2

| ID | Tema | Origen |
|----|------|--------|
| P-L1 | Cron: `sipart:pending-payments-check`, recordatorios sorteo, sorteos nuevos | stubs + spec |
| P-L2 | Autosave diseño, conflictos edición | `resumen_estado_impresion_diseno.md` |
| P-L3 | Firma digital offline asignación vendedor | spec |
| P-L4 | Devoluciones por series/fracciones (información adicional) | spec |
| P-L5 | Diseño externo completo (revocación, 4 semanas, una invitación activa) | spec + `cambios dedign.md` |
| P-L6 | Presupuestos / puntos audios | fuera core |

### 4.5 Duplicados eliminados de listas activas

Los mismos temas aparecían en **`tareas nuevas.md`**, **`ANALISIS_MEJORAS_TAREAS_NUEVAS.md`** y **`nuevas tareas 16-02.md`**. Quedan **una sola vez** en `docs/PENDIENTES_DESARROLLO.md` y en las tablas §4.2–4.3 de este documento.

---

## 5. Automatización: cron vs manual vs evento

| Mecanismo | Qué dispara hoy | Qué falta |
|-----------|-----------------|-----------|
| **Cron Laravel** (`schedule:run`) | Nada (vacío) | Activar comandos `sipart:*` cuando tengan lógica |
| **Jobs/colas** | PDFs, asignación participaciones, resultados sorteo | Colas en producción (`QUEUE_CONNECTION`) |
| **Webhooks** | Stripe (`/api/stripe/webhook`) | Reconciliación periódica pagos (stub) |
| **Acción admin panel** | Publicar resultados lotería (API fetch), notificaciones manuales | — |
| **Eventos de negocio** | Regalo, cobro, invitación, asignación → inbox+FCM | Más eventos si se definen (sorteo próximo, pago entidad…) |

**Conclusión:** No hay verificación automática periódica de “pagos pendientes” ni “sorteos nuevos”; es **manual** o **al guardar** un resultado.

---

## 6. App móvil — estado resumido

| Módulo | Estado |
|--------|--------|
| Auth / roles / guards | ✅ Mejorado (16-02 T24) |
| Venta / QR / un sorteo | ✅ T25 |
| Cartera / regalo / cobro API | ✅ Backend notifica; UI cobro OK |
| Gestor (asignación, vendedores, pago…) | ⚠️ Funcional; loaders/contexto sesión revisados en sesiones recientes |
| Notificaciones | ✅ API; etiquetas kind preparadas |
| Devoluciones | ⚠️ Doc API lista; implementación app según `devoluciones-flujo-para-app.md` |
| Venta digital + email no registrado | ❌/⚠️ `tacos.md`, `devoluciones-participaciones-digitales.md` |

---

## 7. Plan recomendado para cerrar desarrollo

### Fase A — Estabilización (2–3 sprints)
1. P-H1 → P-H4 (PDF + XML + diseño uploads)
2. P-H8, P-M8 (informe vendedores/usuarios)
3. P-M1 (imágenes en vistas)

### Fase B — Producto spec crítico (3–4 sprints)
4. P-H5 + venta digital app (`tacos.md`)
5. P-H6 + P-H7 (magic links + gestor responsable)
6. P-M5 → P-M7 (plantillas, web/TPV, imprimir)

### Fase C — Pulido y operación (1–2 sprints)
7. P-M10, P-M11 (app notificaciones)
8. P-L1 cron + colas producción
9. Regresión E2E panel + app

---

## 8. Mantenimiento documental

| Acción | Archivo |
|--------|---------|
| **Fuente única de pendientes** | `docs/PENDIENTES_DESARROLLO.md` |
| **Histórico completado** | `TAREAS_REALIZADAS.md` |
| **Índice** | `docs/INDICE_DOCUMENTACION.md` |
| Listas antiguas | `tareas nuevas.md`, `nuevas tareas 16-02.md` → solo puntero al índice |

Actualizar este archivo (`AUDITORIA_DESARROLLO_SIPART.md`) al cerrar cada fase o sprint.

---

*Generado por consolidación de documentación interna y revisión de código (mayo 2026).*
