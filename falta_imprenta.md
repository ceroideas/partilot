Lo que falta respecto a la especificación (impresion_original.md / auditoría)
1. Rol / panel de la imprenta colaboradora

No existe login ni vista para que la imprenta gestione sus pedidos.
Hoy lo hace SIPART en Órdenes imprenta.
Falta: usuario imprenta, permisos, bandeja de entrada, archivos/PDF del pedido, etc.
2. Flujo desde el set (no solo desde diseño)

La spec habla de “Enviar a imprimir” en acciones rápidas del set.
Hoy el envío está en Diseño e impresión (design/send-to-print), no en sets/.
Falta: entrada desde set, resumen del set y confirmación al estilo spec.
3. Asignación de tacos como paso de negocio

El formulario pide participaciones por taco y estima tacos en el presupuesto.
No hay el wizard spec: “número de tacos”, generación/confirmación de tacos ligada al envío, ni la tabla desplegable Tacos en el set tras el envío.
4. Pago de la entidad al enviar su diseño

Entidad/admin puede enviar con presupuesto pero sin cobro online en submitPrintOrder.
El cobro Stripe está en el flujo externo PARTILOT, no en “entidad envía set ya diseñado + paga”.
Falta: checkout entidad, conciliación pedido↔pago en todos los caminos, panel de conciliación (bloque 3 del plan).
5. Bloqueos completos tras el envío (spec)

Spec: deshabilitar impresión, descarga y edición.
Hoy: edición y reenvío bloqueados; los PDF en el listado de diseños siguen disponibles con orden en imprenta.
Falta: alinear con spec (o documentar excepción).
6. Condición “solo si hay imprenta configurada”

Spec: el botón solo si hay imprenta vinculada.
No hay comprobación en UI antes de mostrar Enviar a imprenta (solo se usa PrintConfiguration::first() al calcular precio).
7. Seguimiento en el set

Falta informe en el set: fecha envío, estado, detalle de tacos generados, acción rápida Tacos post-impresión.
8. Integraciones (bloque 4 del plan)

Webhooks Stripe robustos, reintentos, sincronización con terceros → pendiente.
9. Desbloqueo tras rechazo

Orden rechazada ya no bloquea edición (bien).
Falta UX clara en set/diseño: “rechazada → puedes corregir y reenviar” y política de descargas.