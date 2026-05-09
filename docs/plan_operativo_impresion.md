# Plan Operativo de Implementación - Módulo de Impresión

## Objetivo

Definir un plan cerrado de ejecución para completar únicamente los pendientes reales del módulo de impresión, partiendo de que el núcleo funcional (editor, generación base y flujo principal) ya está implementado.

## Alcance del plan (enfoque incremental)

- Bloqueos específicos de operación para imprenta y puntos de venta.
- Continuidad de edición (guardado automático real y recuperación).
- Controles de conflicto en pagos/cobros.
- Integraciones técnicas necesarias (webhooks y eventos).
- Ajustes de trazabilidad final donde aplique.

## Bloque 1 - Bloqueos Específicos Imprenta y Punto de Venta

**Objetivo:** reforzar reglas operativas sin rehacer funcionalidades existentes.

**Horas operativas estimadas:** **24 horas**

### Tareas

1. **Bloqueo de edición por estado operativo (imprenta)**
   - Reforzar bloqueo en backend cuando el set entra en estado no editable por operación de imprenta.
   - Sincronizar visibilidad de acciones rápidas según estado real.
   - **Estimación:** 10 horas.

2. **Bloqueos por punto de venta y asignaciones**
   - Reglas de no edición si existen participaciones comprometidas por venta/asignación.
   - Cobertura de escenarios de devolución para posible desbloqueo controlado.
   - **Estimación:** 10 horas.

3. **Auditoría mínima de decisiones de bloqueo/desbloqueo**
   - Registro de actor, causa y timestamp.
   - **Estimación:** 4 horas.

### Entregables del bloque

- Reglas de bloqueo imprenta/PV aplicadas de forma consistente.
- Auditoría de acciones críticas de bloqueo/desbloqueo.

---

## Bloque 2 - Guardado Automático y Recuperación de Trabajo

**Objetivo:** asegurar continuidad de edición real y evitar pérdida de trabajo.

**Horas operativas estimadas:** **28 horas**

### Tareas

1. **Autosave backend transaccional**
   - Persistencia incremental por intervalos y por eventos de cambio relevantes.
   - Endpoint dedicado de guardado parcial.
   - **Estimación:** 14 horas.

2. **Recuperación de sesión y continuidad**
   - Reanudar último estado guardado o iniciar limpio manteniendo datos base del set.
   - **Estimación:** 8 horas.

3. **Controles de conflicto de edición**
   - Prevención de sobrescritura en edición simultánea básica.
   - Mensajería de conflicto y resolución simple (última versión o recarga).
   - **Estimación:** 6 horas.

### Entregables del bloque

- Autosave operativo en servidor.
- Recuperación de trabajo robusta y control de conflictos de edición.

---

## Bloque 3 - Pagos, Conflictos y Trazabilidad Operativa

**Objetivo:** asegurar consistencia de cobros y conflictos en procesos de impresión.

**Horas operativas estimadas:** **36 horas**

### Tareas

1. **Controles de conflicto en pagos**
   - Manejo de pagos duplicados, pagos en carrera y estados intermedios.
   - Idempotencia en operaciones de confirmación.
   - **Estimación:** 14 horas.

2. **Conciliación de estado pedido-pago**
   - Reglas de transición entre estado de pedido de imprenta y estado de cobro.
   - **Estimación:** 10 horas.

3. **Trazabilidad reforzada**
   - Registro de eventos críticos (intento, confirmación, error, reversión).
   - **Estimación:** 12 horas.

### Entregables del bloque

- Flujo de cobro estable frente a concurrencia y errores.
- Historial operativo completo para soporte y auditoría.

---

## Bloque 4 - Integraciones y Webhooks

**Objetivo:** cerrar integración técnica con servicios externos y eventos asíncronos.

**Horas operativas estimadas:** **30 horas**

### Tareas

1. **Diseño e implementación de webhooks**
   - Contratos de evento para pago, impresión y estados de pedido.
   - Validación de firma y seguridad de recepción.
   - **Estimación:** 12 horas.

2. **Reintentos y tolerancia a fallos**
   - Cola de reintentos para eventos fallidos.
   - Alertas básicas de integración.
   - **Estimación:** 8 horas.

3. **Sincronización final de estados**
   - Homologación de estados internos con respuestas de terceros.
   - Ajustes en panel para visibilidad operativa.
   - **Estimación:** 10 horas.

### Entregables del bloque

- Integraciones robustas por webhook.
- Operación estable ante fallos de terceros.

---

## Resumen global

- **Bloque 1:** 24 horas
- **Bloque 2:** 28 horas
- **Bloque 3:** 36 horas
- **Bloque 4:** 30 horas

### Total estimado del plan completo

**118 horas operativas**

## Estado actual de ejecución

- Bloque 1 (operativo): **completado**.
  - Bloqueos por estado operativo de set e imprenta.
  - Acciones rápidas sincronizadas según estado real.
  - Estados de orden de imprenta (`pendiente_revision`, `en_produccion`, `enviada`, `rechazada`) con transición controlada.
  - Auditoría mínima de bloqueo y auditoría de cambios de estado de orden.
- Bloque 2 (continuidad/autosave): **completado**.
  - Guardado automático operativo.
  - Recuperación de borrador persistente por set.
  - Control de conflicto básico en edición concurrente.

### Pendiente (solo económico e integraciones)

- Bloque 3: pagos, conciliación y trazabilidad de cobro — **en curso (base aplicada)**:
  - **El cobro con Stripe no cambia en esencia:** sigue verificándose el PaymentIntent contra la API y creándose el pedido igual; solo se añadió control anti-duplicado, bloqueo y auditoría (no se alteró la pasarela ni el importe cobrado por Stripe salvo lo indicado abajo).
  - Idempotencia por `payment_intent_id` (Stripe) + bloqueo distribuido + transacción con `lockForUpdate`.
  - Auditoría de intento duplicado (`duplicate_payment_intent_blocked`) y de alta (`order_created_stripe` / `order_created_internal`).
  - Pedidos internos (`submitPrintOrder`) con `payment_status = not_required` y trazabilidad en tabla de auditoría.
  - Columna **Cobro** en órdenes imprenta (estado de pago + fecha de cobro si existe).
  - **Presupuesto “enviar a imprenta” desde el panel:** la línea de **tarifa de diseño** no suma al total cuando el pedido sale del diseño ya elaborado en PARTILOT (`calculatePrintOrderQuote` con diseño exento). El flujo **invitación externa / pago directo** (`calculateExternalInvitationQuote`) **sigue incluyendo** la tarifa de diseño.
  - Pendiente de negocio: reglas fuertes pedido↔pago en todos los flujos, idempotencia global de referencias de cobro, panel de conciliación.
- Bloque 4: webhooks, reintentos e integración externa de estados.

## Dependencias clave para ejecución

1. Validación funcional final de reglas de bloqueo (imprenta y punto de venta).
2. Confirmación de políticas de conflicto y conciliación de pagos.
3. Definición del contrato técnico de webhooks e integraciones externas.
4. Confirmación de roles con permisos para operaciones críticas.

## Recomendación de ejecución

1. Ejecutar primero Bloques 1 y 2 para asegurar integridad operativa y continuidad.
2. Continuar con Bloque 3 para estabilizar cobros y conflictos.
3. Finalizar con Bloque 4 para robustecer integraciones externas.

