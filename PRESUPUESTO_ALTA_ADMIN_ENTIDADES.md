# Presupuesto: Alta de Administración de Lotería y Alta de Entidad / Gestión de Responsables

**Referencia:** Especificación funcional 1 (Alta Administración) y 2 (Alta Entidad y Gestión de Responsables)  
**Base:** Sistema actual PARTILOT (Laravel backend + app móvil Partilotapp)

---

## Resumen del estado actual

| Área | Lo que hay ahora | Brecha principal |
|------|------------------|------------------|
| **Administraciones** | Alta multi-paso (datos oficina + “gestor”); el gestor se crea como User + Manager y accede por email/contraseña fija (ej. 12345678). | No hay “gestor informativo” solo contacto; no hay usuario inalterable; no hay envío manual de credenciales ni Magic Link; login es por email, no por usuario generado. |
| **Entidades** | Alta con gestor obligatorio (User + Manager con `is_primary`). Invitación de gestores con `status` null (pendiente). Cambio de responsable sin flujo de aceptación. | No hay flujo “aceptar/denegar” (email + registro o push); la entidad no se bloquea si el responsable no acepta; no hay intercambio con aceptación explícita; no hay restricción por “solo gestor responsable” en acciones críticas. |

---

## 1. Alta de Administración de Lotería

### 1.1 Gestor informativo (solo contacto)

- **Descripción:** Los datos del gestor de la administración son solo de contacto/soporte. No se crea usuario en la plataforma para esa persona.
- **Tareas:** Campos de contacto del gestor en el alta/edición de administración (o tabla dedicada); no crear User/Manager para ese gestor; ajustar vistas y flujo de alta para que el “paso gestor” sea solo informativo.
- **Estimación:** **6–8 h**

### 1.2 Usuario inalterable (generado por sistema)

- **Descripción:** Un único usuario de acceso por administración, generado por el sistema:
  - Administración de Lotería: número de receptor + 3 últimos dígitos del número de administración.
  - Punto de venta mixto: solo número de receptor.
- **Tareas:** Migración `username` en `users` (nullable, único); lógica de generación según tipo de punto de venta; al dar de alta la administración, crear 1 User con ese `username`, email de la administración y sin contraseña hasta Magic Link; adaptar login (Auth) para permitir inicio de sesión por `username` para rol administración.
- **Estimación:** **10–12 h**

### 1.3 Envío manual de credenciales y Magic Link (Superadministrador)

- **Descripción:** Solo el Superadministrador puede enviar credenciales. No se envía nada al crear el alta. El correo incluye usuario y enlace mágico para establecer (o restablecer) contraseña.
- **Tareas:** Botón “Enviar credenciales” en ficha de administración (solo superadmin); generación de token (tabla tipo `password_reset_tokens` o equivalente) y URL firmada; envío de email con usuario + Magic Link; ruta pública que reciba el enlace y muestre formulario “Establecer contraseña”; al guardar, actualizar contraseña del User y marcar token como usado; botón disponible siempre (primer acceso y reseteo).
- **Estimación:** **12–14 h**

### 1.4 Seguridad y privacidad

- **Descripción:** Contraseña siempre encriptada y nunca mostrada. Superadmin puede forzar cambio de contraseña. La administración puede cambiar su contraseña desde su panel (actual + nueva + confirmación).
- **Tareas:** En ficha administración (vista superadmin): campo usuario (solo lectura), campo “Nueva contraseña” (opcional) para que superadmin pueda forzar cambio; nunca mostrar contraseña actual. En panel de la administración (sección datos): mostrar usuario (solo lectura) y formulario “Cambiar contraseña” (contraseña actual, nueva, repetir nueva) con validación.
- **Estimación:** **6–8 h**

**Subtotal bloque 1 (Administración de Lotería):** **34–42 h**

---

## 2. Alta de Entidad y Gestión de Responsables

### 2.1 Gestor responsable obligatorio y estados de aceptación

- **Descripción:** Siempre debe haber un gestor responsable (`is_primary`). Debe ser usuario registrado en PARTILOT y debe haber aceptado explícitamente. Si no hay responsable aceptado, la entidad no puede seguir con impresión, asignación de participaciones, etc.
- **Tareas:** Definir estados del Manager para aceptación (ej. `pending`, `accepted`, `denied`); al crear o asignar gestor responsable, dejar en `pending` hasta aceptación; reglas de negocio y middleware/checks: bloquear flujos críticos (impresión, asignación, etc.) si la entidad no tiene al menos un gestor con `is_primary` y estado `accepted`; mensajes claros en UI cuando la entidad esté bloqueada.
- **Estimación:** **10–12 h**

### 2.2 Vinculación cuando el gestor aún no es usuario

- **Descripción:** Si el email del gestor responsable no tiene usuario en la plataforma: enviar email indicando que debe registrarse con ese email; al registrarse (con ese email), vincularlo al Manager y notificar que ha sido agregado como gestor responsable y debe aceptar; si no acepta, estado denegado y entidad bloqueada.
- **Tareas:** Flujo de alta de entidad: si email no existe como User, crear Manager con `user_id` null y email guardado; cola o envío de email “regístrese con este email…”; en el registro (API/app), detectar si existe invitación pendiente por email y vincular `user_id` al Manager; notificación (en app o email) para “aceptar rol”; endpoint (web o API) aceptar/denegar; actualizar estado y desbloquear/bloquear entidad según corresponda.
- **Estimación:** **14–18 h**

### 2.3 Vinculación cuando el gestor ya es usuario (notificación en APP)

- **Descripción:** Si ya es usuario: notificación push en la app para aceptar el nuevo rol. Si no acepta, estado denegado y entidad bloqueada.
- **Tareas:** Al asignar gestor responsable ya existente (mismo User), crear Manager en `pending` y enviar notificación push (FCM) al usuario; en la app: pantalla o modal “Te han agregado como gestor responsable de [Entidad]. ¿Aceptas?” con botones Aceptar/Denegar; llamada API para aceptar/denegar; actualizar estado en backend y reflejar bloqueo de entidad si corresponde.
- **Estimación:** **10–14 h** (backend 4–6 h, app 6–8 h)

### 2.4 Jerarquía de gestores

- **Descripción:** El gestor responsable puede crear otros gestores y asignarles permisos.
- **Tareas:** Revisar y completar UI de “invitar gestor” / “añadir gestor” (permisos: sellers, design, statistics, payments, etc.); asegurar que solo el responsable (o superadmin) pueda asignar/editar otros gestores según reglas de negocio.
- **Estimación:** **4–6 h**

### 2.5 Intercambio de responsabilidad

- **Descripción:** Para cambiar al gestor responsable debe existir otro gestor que asuma el rol. El nuevo responsable debe aceptar explícitamente (email informativo y, si aplica, aceptación en app). Mientras no acepte, sigue el antiguo como responsable.
- **Tareas:** Flujo en backend: solo permitir “cambiar responsable” si hay al menos otro Manager de la entidad; al elegir nuevo responsable, poner el actual en no-primary y el nuevo en primary con estado `pending`; enviar email (y push si es usuario) al nuevo para que acepte; endpoint aceptar: al aceptar, confirmar primary y estado accepted; si hay tiempo de espera o rechazo, revertir y mantener antiguo como primary; UI en listado/edición de gestores para “Hacer responsable” con este flujo.
- **Estimación:** **12–16 h**

### 2.6 Limitaciones de rol (solo gestor responsable)

- **Descripción:** Acciones críticas (por ejemplo “transmisión de la devolución a la administración”) solo las puede realizar el gestor responsable.
- **Tareas:** Identificar en el código todas las acciones que deban estar restringidas (devoluciones a administración, y otras que se definan); middleware o comprobaciones `auth()->user()` + Manager `is_primary` y estado `accepted`; devolver 403 y mensaje claro si un gestor no responsable intenta la acción.
- **Estimación:** **6–8 h**

**Subtotal bloque 2 (Entidad y responsables):** **56–74 h**

---

## Resumen de horas estimadas

| Bloque | Horas (mín–máx) |
|--------|------------------|
| 1. Alta de Administración de Lotería | 34–42 h |
| 2. Alta de Entidad y Gestión de Responsables | 56–74 h |
| **Total** | **90–116 h** |

---

## Consideraciones para el presupuesto económico

- **Tarifa horaria:** A aplicar según tu convenio (ej. 35–55 €/h según seniority y mercado).
- **Ejemplo:** A 45 €/h → **4.050 € – 5.220 €** (solo desarrollo).
- **No incluido en esta estimación:** Diseño UX/UI específico, pruebas E2E exhaustivas, documentación de usuario final, formación, despliegue en producción ni partidas de infraestructura (email, FCM, etc.). Si se incluyen, sumar las horas correspondientes.
- **Riesgo:** La estimación asume que el tipo “Punto de venta mixto” vs “Administración de Lotería” ya existe o se puede derivar de datos existentes (ej. `admin_number`); si requiere nuevo modelo o flujos, puede sumar 4–8 h.

---

## Orden sugerido de implementación

1. **Fase 1 – Administración:** 1.1 Gestor informativo → 1.2 Usuario inalterable → 1.3 Magic Link y botón envío → 1.4 Seguridad y paneles (usuario/contraseña).
2. **Fase 2 – Entidad (base):** 2.1 Estados de aceptación y bloqueo de entidad → 2.6 Restricción por gestor responsable.
3. **Fase 3 – Entidad (flujos):** 2.2 Gestor no usuario (email + registro + aceptar) → 2.3 Gestor ya usuario (push + aceptar en app) → 2.5 Intercambio de responsabilidad.
4. **Fase 4:** 2.4 Jerarquía de gestores y pulido de permisos.

Si quieres, el siguiente paso puede ser bajar esto a tareas concretas por sprint (con prioridades y dependencias) o ajustar horas por partida según tu tarifa y alcance.
