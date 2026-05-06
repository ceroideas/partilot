# Resumen Estado Actual - Diseño e Impresión

## 1) Funcionalidad ya implementada

### Flujo base de diseño
- Existe un flujo operativo para entrar al módulo de diseño y trabajar sobre sets.
- El proceso de diseño está integrado en el circuito actual del sistema.

### Editor gráfico
- El editor ya permite trabajar con elementos visuales y composición del diseño.
- Se soporta maquetación con parámetros técnicos de impresión (área útil, disposición y ajustes relacionados).
- Hay soporte para participación, portada/tapas y reverso en el flujo de diseño.

### Persistencia y reutilización
- El diseño se guarda y puede retomarse posteriormente.
- Existe reutilización de diseños previos para acelerar nuevos trabajos.
- Se dispone de snapshots/vistas previas dentro del flujo.

### Generación de salidas
- Ya existe generación de PDF en las salidas principales del módulo.
- El sistema contempla variantes de exportación del material diseñado.

### Permisos y acceso
- Hay control de acceso al módulo por permisos/roles.
- El uso del módulo de diseño está ligado al contexto operativo de entidad/gestión.

### Soporte de diseño externo
- Existe un flujo de colaboración externa por invitación/enlace.
- Se contempla intercambio de material y seguimiento básico del trabajo externo.

---

## 2) Nuevos pendientes (separados del núcleo ya hecho)

### Bloqueos operativos específicos
- Reforzar reglas de bloqueo por estado real de operación (imprenta y punto de venta).
- Asegurar coherencia total entre estado del set y acciones habilitadas en pantalla.
- Trazar de forma clara acciones de bloqueo/desbloqueo y su motivo.

### Guardado automático y continuidad
- Completar autosave real en servidor durante la edición activa.
- Mejorar recuperación automática del trabajo ante interrupciones.
- Añadir control de conflictos de edición simultánea en casos críticos.

### Pagos y consistencia transaccional
- Añadir controles de conflicto en cobro (duplicidad, carrera de estados, confirmaciones repetidas).
- Alinear de forma robusta estado de pedido de impresión con estado de pago.
- Reforzar trazabilidad de eventos de cobro y cambios de estado.

### Webhooks e integración
- Definir y cerrar contratos de eventos con sistemas externos.
- Incorporar validación de seguridad de eventos entrantes.
- Implementar reintentos y tolerancia a fallos de integración.
- Homologar estados internos con respuestas de terceros.

---

## 3) Lectura ejecutiva

- El núcleo de diseño e impresión **ya está construido y operativo**.
- El trabajo nuevo se concentra en **robustez operativa**, no en rehacer el editor.
- Los pendientes actuales son principalmente de:
  - reglas de bloqueo de negocio,
  - continuidad/autosave,
  - consistencia de pagos,
  - integración técnica por webhooks.

