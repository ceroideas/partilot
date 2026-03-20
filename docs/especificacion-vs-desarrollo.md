
### 1.1 Diseño de participaciones (Se requiere plantillas - 2 días de trabajo)

- **Diseño por plantillas**: La especificación indica dos métodos, "Diseño por plantillas" y "Diseño personalizado". Las plantillas serían:
  - SIPART, TIKET GESTIÓN, ASG (diseño preestablecido, dimensiones, márgenes, sangres, matriz; solo logotipo y algunos textos personalizables).
  - Opción de elegir plantilla para la parte trasera (si no se selecciona, plantilla predeterminada SIPART).
- En el código solo existe la elección "Diseño" (ir al formato) o "Enviar a diseñar". No hay flujo específico "por plantillas" con esas tres marcas ni selector de plantilla para el reverso.

### 1.2 Generación de Web de venta / TPV e iframes (Se requiere información de que se haría aqui)

- **Proceso de generación de Web de venta**: Formulario TPV para la entidad, logotipo e imagen de cabecera, revisión por administrador SIPART, generación de iframe y notificación a la entidad con código e instrucciones.
- **Iframes de pago**: Generación según perfil de administración; iframe básico (solo web) vs completo (con API); verificación de sesión vía API, códigos de recarga con opción "hacer efectivo" en la web de la administración.
- Existe `SocialWebController` y rutas `social` (solicitudes/webs). Falta contrastar si cubren exactamente el flujo de la spec (solicitud TPV → revisión admin → generación iframe + email con código) y el comportamiento de iframe básico/completo.

### 1.3 Enviar a diseñar (invitación externa) - Lunes 23/03

- **Acceso temporal 4 semanas**: "El link generado será válido por un máximo de 4 semanas" y "Transcurrido este tiempo, las credenciales serán eliminadas".
- **Invitación única**: "Solo una persona puede ser invitada a diseñar un set a la vez; revocar y enviar nueva". Verificar si existe revocación explícita y bloqueo de una sola invitación activa.

### 1.4 Enviar a imprimir - Jueves 26/03

- **Flujo completo**: La spec describe "Enviar a Imprimir" desde administración (asignación de tacos, resumen, envío a imprenta) y desde entidad (asignación tacos, resumen, importe, pago, confirmación). Tras el envío, deshabilitar impresión, descarga y edición (y reactivar si la imprenta rechaza o lo permite administración/SIPART).
- **Seguimiento**: Informe de estado (en revisión / en producción / enviado).
- En el código hay referencias a imprenta en configuración (`ordenes-imprenta`, `imprenta`), pero no hay rutas ni flujo claro de "Enviar a imprimir" por set con asignación de tacos, pago entidad, estados y bloqueo de acciones.

### 1.5 Devoluciones por series y fracciones - (Se requiere mas información sobre este tema)

- **Se requiere información adicional sobre este punto** (Se revisará y se tendrá para el 30/03)

### 1.6 Revisión de diseño en panel y aplicación


- Presupuestos nuevos (Falta por revisarlos)

### 1.5 Asignación de tacos/participaciones a vendedor

- **Modo offline**: "Ventana de firma; el vendedor firma digitalmente; el documento firmado se envía al correo de la entidad." Comprobar si está implementado.
- **Notificación y aceptación** del vendedor cuando está registrado (y flujo de aceptación en app).

Más información de tema de firmas y facturación