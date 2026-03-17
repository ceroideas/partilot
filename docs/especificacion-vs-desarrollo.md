# Análisis: Especificación vs Desarrollo SIPART

## 1. Lo que **falta por desarrollar** (según especificación)

### 1.1 Administraciones

- **Sección "permisos y opciones que se acuerdan con la administración"**: Desarrollar limite segun permisos dados al gestor.

### 1.2 Reservas

- **Email al gestor de la entidad** al guardar la reserva con: sorteo reservado, cantidad de dinero reservado, series y fracciones asignadas. No aparece envío de email en el flujo de creación de reserva en el código.

### 1.3 Sets de participaciones

- **Email al gestor de la entidad** con los detalles del set al crearlo. No se ve ese envío en el flujo de creación de sets.

### 1.4 Diseño de participaciones

- **Diseño por plantillas**: La especificación indica dos métodos, "Diseño por plantillas" y "Diseño personalizado". Las plantillas serían:
  - SIPART, TIKET GESTIÓN, ASG (diseño preestablecido, dimensiones, márgenes, sangres, matriz; solo logotipo y algunos textos personalizables).
  - Opción de elegir plantilla para la parte trasera (si no se selecciona, plantilla predeterminada SIPART).
- En el código solo existe la elección "Diseño" (ir al formato) o "Enviar a diseñar". No hay flujo específico "por plantillas" con esas tres marcas ni selector de plantilla para el reverso.

### 1.6 Generación de Web de venta / TPV e iframes

- **Proceso de generación de Web de venta**: Formulario TPV para la entidad, logotipo e imagen de cabecera, revisión por administrador SIPART, generación de iframe y notificación a la entidad con código e instrucciones.
- **Iframes de pago**: Generación según perfil de administración; iframe básico (solo web) vs completo (con API); verificación de sesión vía API, códigos de recarga con opción "hacer efectivo" en la web de la administración.
- Existe `SocialWebController` y rutas `social` (solicitudes/webs). Falta contrastar si cubren exactamente el flujo de la spec (solicitud TPV → revisión admin → generación iframe + email con código) y el comportamiento de iframe básico/completo.

### 1.7 Enviar a diseñar (invitación externa)

- **Acceso temporal 4 semanas**: "El link generado será válido por un máximo de 4 semanas" y "Transcurrido este tiempo, las credenciales serán eliminadas".
- **Invitación única**: "Solo una persona puede ser invitada a diseñar un set a la vez; revocar y enviar nueva". Verificar si existe revocación explícita y bloqueo de una sola invitación activa.

### 1.8 Enviar a imprimir

- **Flujo completo**: La spec describe "Enviar a Imprimir" desde administración (asignación de tacos, resumen, envío a imprenta) y desde entidad (asignación tacos, resumen, importe, pago, confirmación). Tras el envío, deshabilitar impresión, descarga y edición (y reactivar si la imprenta rechaza o lo permite administración/SIPART).
- **Seguimiento**: Informe de estado (en revisión / en producción / enviado).
- En el código hay referencias a imprenta en configuración (`ordenes-imprenta`, `imprenta`), pero no hay rutas ni flujo claro de "Enviar a imprimir" por set con asignación de tacos, pago entidad, estados y bloqueo de acciones.

### 1.10 Transmisión de devolución a la administración

- **Comunicación**: "El sistema enviará notificación al panel de la administración, email con resumen de la devolución y listado de participaciones devueltas" y "este mismo informe a la dirección de email de la entidad".
- Hay controlador y rutas de devoluciones. Falta confirmar si, al transmitir, se envían esos emails y notificación al panel.

### 1.11 Asignación de tacos/participaciones a vendedor

- **Modo offline**: "Ventana de firma; el vendedor firma digitalmente; el documento firmado se envía al correo de la entidad." Comprobar si está implementado.
- **Notificación y aceptación** del vendedor cuando está registrado (y flujo de aceptación en app).

---

## 2. Lo que está **desarrollado y es adicional** (o distinto) a la spec

### 2.1 Roles y contexto

- **Selector de rol** ("Entrar como Gestor de Administración" / "Gestor de Entidad") y filtrado por `accessibleEntityIds()` en diseño, listados, etc. No está descrito así en la spec; es una ampliación del modelo de permisos.

### 2.2 Enviar a diseñar (implementación actual)

- **Acceso por enlace sin login**: El invitado usa solo el enlace del correo (token), sin usuario/contraseña. La spec habla de "usuario y contraseña" y "login exclusivo".
- **Sin expiración 4 semanas** ni eliminación de credenciales por tiempo.
- Se puede considerar "adicional" en cuanto a UX (más simple) pero "distinto" respecto a seguridad y plazos de la spec.

### 2.3 Diseño e impresión

- **Editor avanzado** (format/edit_format) con fondos, imágenes, textos, QR, portada, reverso, márgenes, matriz, exportación PDF por participación/portada/reverso y exportación masiva. La spec lo contempla como "diseño personalizado"; no hay conflicto, es el desarrollo de esa parte.
- **Reutilización de diseños** de la entidad (`listFormats`): La spec no lo detalla; es una mejora sobre "guardar y usar el diseño".

### 2.4 Configuración y operativa

- **Órdenes de pago SEPA** (entidades, beneficiarios, generación XML). No aparece en la spec; es funcionalidad extra de tesorería.
- **Configuración** (imprenta, factura automática, etc.): La spec menciona imprenta asociada pero no este nivel de configuración.
- **Comunicaciones** (`communications.index`): Vista presente; la spec no la detalla.
- **Solicitudes** (`requests`): Posible flujo de solicitudes (ej. TPV/iframes); no descrito en la spec.

### 2.5 Sorteos y premios

- **Tipos de sorteo** (precio décimo, premio serie/fracción), **sorteos** (imagen, número, fechas, tipo), **resultados** (fetch API Loterías, edición), **escrutinio por administración** y **categorías de premio**. La spec sí habla de configuración de sorteos, tipos y asignación de premios; esto alinea con ella, con posible ampliación en escrutinio y categorías.

### 2.6 App / API

- **Cobro, premios, participaciones, escrutinio por número**, etc. en API: encajan con "Proceso de cobro" y "Escrutinio" de la spec; pueden incluir detalles no literales (endpoints, estructura) pero no se consideran "adicionales" de concepto.

### 2.7 Notificaciones

- **Notificaciones push (Firebase)** y panel de notificaciones: La spec habla de "notificación a la aplicación" en varios procesos; esto es la implementación de ese canal.

### 2.8 Grupos

- **Grupos de vendedores** (GroupController, group_seller): la spec no menciona grupos; es una extensión del modelo de vendedores.

### 2.9 Historial y auditoría

- **Historial de actividades de participaciones** (`ParticipationActivityLogController`) y **activity-logs** (por vendedor, entidad, estadísticas, recientes): La spec no lo detalla; es valor añadido para trazabilidad.
