# WhatsApp en producción — Guía para el cliente (Partilot)

Este documento resume **qué debe hacer vuestra organización** (no el desarrollador de la app) para que los vendedores puedan enviar por WhatsApp el mensaje con **código de vinculación** y **enlace de registro** al comprador, **a cualquier número**, desde la app Partilot.

---

## Situación actual (pruebas)

Con la configuración de **sandbox** de Twilio:

- Solo funciona con el número de prueba de Twilio (`+1 415 523 8886`).
- Cada teléfono de prueba debe enviar antes un mensaje tipo **`join <código>`** al sandbox (lo indica la consola de Twilio).
- **No sirve para clientes reales** en producción.

La app y el servidor ya están preparados; falta **activar WhatsApp Business en producción** y configurar el servidor.

---

## Objetivo en producción

Que el vendedor, tras una venta digital sin email (o desde el historial), introduzca el móvil del comprador y el sistema envíe automáticamente un WhatsApp como:

> Hola. Te he vendido X participaciones digitales de [Entidad] ([Sorteo]).  
> El código para reclamarlas en la app Partilot es: **XXXXXX**  
> En la app: Cartera → Vincular con código.  
> Puedes registrarte desde aquí: [enlace]

Eso requiere **Twilio + WhatsApp Business (Meta)** aprobados.

---

## Checklist — Qué debe hacer el cliente

### 1. Cuentas necesarias

| Cuenta | Para qué |
|--------|----------|
| **Twilio** | Envío de mensajes (API que usa Partilot) |
| **Meta Business** (Facebook Business) | WhatsApp Business vinculado al número remitente |

Recomendación: usar el mismo correo o equipo técnico/administrativo para ambas cuentas.

---

### 2. Twilio — Credenciales y WhatsApp

1. Entrar en [Twilio Console](https://console.twilio.com/).
2. Anotar (o regenerar si hace falta):
   - **Account SID** (empieza por `AC…`)
   - **Auth Token**
3. Ir a **Messaging** → **Try it out** / **Senders** → **WhatsApp**.
4. Seguir el asistente para **conectar WhatsApp Business** (integración con Meta).
5. **Registrar un número de teléfono propio** como remitente WhatsApp (no el `+1 415 523 8886` de pruebas).
   - Suele ser un móvil o línea del negocio, según país y política de Meta.
6. Completar la **verificación del negocio** en Meta si la solicitan (puede tardar varios días).

**Entregable al equipo técnico:** SID, Auth Token y número aprobado en formato  
`whatsapp:+34XXXXXXXXX` (con prefijo internacional).

---

### 3. Meta — Verificación del negocio

1. En [Meta Business Suite](https://business.facebook.com/), crear o usar la cuenta del negocio (asociación, administración de lotería, etc.).
2. Completar datos legales, web o documentación que pidan.
3. Vincular el **perfil de WhatsApp Business** al número que usaréis para enviar.
4. Esperar aprobación. Sin esto, Twilio no podrá enviar en producción a números arbitrarios.

---

### 4. Plantillas de mensaje (WhatsApp) — obligatorio en producción para este uso

#### ¿Texto libre o plantilla?

| Modo | Cuándo funciona |
|------|------------------|
| **Texto libre** (`WHATSAPP_USE_TEMPLATE=false`, mensaje `body`) | Sandbox de pruebas; o si el comprador **os escribió** a vuestro WhatsApp en las **últimas 24 h** |
| **Plantilla** (`contentSid` + variables) | **Producción recomendada**: el vendedor envía el WhatsApp **sin** que el comprador haya iniciado chat |

En Partilot el vendedor introduce el móvil y el sistema envía el aviso (código + enlace). Eso es un mensaje **iniciado por la empresa**. Meta exige plantilla aprobada en la práctica total de los casos en producción.

**No es obligatorio en el código** (se puede dejar `WHATSAPP_USE_TEMPLATE=false`), pero **sí en las reglas de WhatsApp** para enviar a compradores “en frío”. Si se intenta solo `body` en producción, muchos envíos fallarán.

#### Dónde se crea la plantilla (dos vías, una sola cuenta Meta)

Lo habitual con Twilio es crear la plantilla en **Twilio** (sincronizada con Meta). También se puede crear en **Meta** y usarla vía Twilio una vez vinculadas las cuentas.

**Opción A — Recomendada: Twilio Console**

1. [Twilio Console](https://console.twilio.com/) → **Messaging** → **Content Template Builder**  
   (Ruta alternativa: **Explore Products** → **Content Template Builder**)
2. **Create new** → tipo **WhatsApp**.
3. Idioma **Spanish (es)**.
4. Categoría sugerida: **Utility** (aviso transaccional: venta / reclamación de participación). Evitar “Marketing” si no aplica promoción.
5. Redactar el cuerpo con **variables** `{{1}}`, `{{2}}`, `{{3}}` (Twilio usa números; Partilot ya envía esas tres posiciones).
6. **Submit for WhatsApp approval** → estado *Pending* → *Approved* (desde horas hasta varios días; a veces piden cambios de texto).
7. Cuando esté aprobada, abrir la plantilla y copiar el **Content SID** (`HX…`).

**Opción B: Meta (WhatsApp Manager)**

1. [Meta Business Suite](https://business.facebook.com/) → **Configuración** → **Cuentas de WhatsApp** → **WhatsApp Manager**.
2. **Plantillas de mensajes** → **Crear plantilla**.
3. Mismo criterio de categoría y texto; tras aprobación, en Twilio debe aparecer disponible si el número y la cuenta Business están vinculados.

Si la plantilla se crea solo en Meta con nombres de variables distintos, hay que avisar al desarrollador para alinear el servidor (ver tabla abajo).

#### Texto orientativo para la plantilla

Ejemplo alineado con el mensaje que ya genera Partilot (ajustar redacción; Meta puede rechazar palabras promocionales o URLs mal formadas):

```
Hola. Te he vendido {{1}} participaciones digitales.

El código para reclamarlas en la app Partilot es: {{2}}

En la app: Cartera → Vincular con código.
Regístrate aquí: {{3}}
```

**Importante:** en Meta/Twilio las variables suelen ser **sin** espacios raros; el enlace `{{3}}` debe ser URL válida (https://…). Si rechazan la plantilla, probar texto más corto o categoría Utility.

#### Variables que usa Partilot hoy (servidor)

El backend envía exactamente esto cuando `WHATSAPP_USE_TEMPLATE=true`:

| Variable Twilio | Contenido que envía Partilot |
|-----------------|------------------------------|
| `{{1}}` | Cantidad de participaciones (número, ej. `5`) |
| `{{2}}` | Código de vinculación (ej. `w6ABJl`) |
| `{{3}}` | URL de registro del comprador (sin código en la URL) |

Si la plantilla usa **otro orden** o más variables, el desarrollador debe adaptar `DigitalSaleWhatsAppService.php`.

**Entregables al equipo técnico:**

- `TWILIO_WHATSAPP_CONTENT_SID=HX…`
- Captura o export de la plantilla **aprobada**
- Confirmación de que `{{1}}`, `{{2}}`, `{{3}}` coinciden con la tabla anterior

#### Si la plantilla falla

Partilot intenta un **reintento con texto libre** (`body`). En producción ese reintento **suele fallar** si Meta exige plantilla. Revisar el error en Twilio → **Monitor** → **Logs** → **Messaging**.

#### Pruebas: sandbox vs producción

| Entorno | Plantilla |
|---------|-----------|
| Sandbox | Opcional; suele bastar `WHATSAPP_USE_TEMPLATE=false` |
| Producción | **Recomendado** `WHATSAPP_USE_TEMPLATE=true` + SID aprobado |

---

### 5. Configuración en el servidor Partilot (`.env`)

El equipo que despliega Laravel debe poner en el `.env` del servidor (valores reales, no de ejemplo):

```env
WHATSAPP_ENABLED=true
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_WHATSAPP_FROM=whatsapp:+34XXXXXXXXX
WHATSAPP_DEFAULT_COUNTRY_CODE=34

# Producción con plantilla aprobada (recomendado):
WHATSAPP_USE_TEMPLATE=true
TWILIO_WHATSAPP_CONTENT_SID=HXxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

Después, en el servidor:

```bash
php artisan config:clear
```

(Reiniciar PHP/Apache si aplica.)

---

### 6. Pruebas antes de abrir a todos los vendedores

1. Con plantilla y número de producción aprobados, hacer **una venta digital sin email** en la app.
2. En el modal de éxito (o historial), enviar WhatsApp a un móvil de prueba **que no haya usado el sandbox**.
3. Comprobar que llega código + enlace y que el comprador puede registrarse o vincular en la app.
4. Revisar en Twilio **Messaging → Logs** si algún envío falla (plantilla, número, límite de país, etc.).

---

## Qué NO debe hacer el cliente

- No compartir SID/Token en correos públicos ni subirlos a repositorios.
- No usar en producción el número sandbox `+1 415 523 8886` como remitente.
- No esperar que “cualquier número” funcione sin verificación Meta ni sin plantilla (si Meta lo exige para ese tipo de mensaje).
- No dar el código de vinculación al vendedor en la app (está oculto a propósito); solo al comprador por WhatsApp, email o soporte desde el panel (superadmin / administración / entidad).

---

## Resumen en una frase

**El cliente debe:** verificar el negocio en Meta, registrar un número WhatsApp Business en Twilio, aprobar una plantilla de mensaje, y entregar SID, Token, número remitente y Content SID al equipo técnico para el `.env` de producción.

**Entonces** los vendedores podrán enviar desde Partilot al teléfono que indiquen, sin que el comprador tenga que hacer `join` al sandbox.

---

## Contacto técnico — Datos a entregar

Rellenar y enviar al desarrollador/hosting:

| Dato | Valor |
|------|--------|
| Account SID | |
| Auth Token | |
| TWILIO_WHATSAPP_FROM | `whatsapp:+…` |
| TWILIO_WHATSAPP_CONTENT_SID (si plantilla) | `HX…` |
| ¿Plantilla aprobada? Sí / No | |
| País principal de compradores | ej. España (+34) |
| Fecha prevista de puesta en producción | |

---

## Referencias útiles

- [Twilio — WhatsApp](https://www.twilio.com/docs/whatsapp)
- [Twilio — Sandbox vs producción](https://www.twilio.com/docs/whatsapp/sandbox)
- [Twilio — Content Template Builder](https://www.twilio.com/docs/content/content-template-builder)
- [Twilio — Send WhatsApp with Content API](https://www.twilio.com/docs/content/whatsapp-send-content-template-messages)
- [Meta — WhatsApp Business Platform](https://developers.facebook.com/docs/whatsapp)
- [Meta — Plantillas de mensajes](https://developers.facebook.com/docs/whatsapp/message-templates)

---

---

## Alternativa más simple: SMS (Twilio, mismo Account SID)

Si no podéis completar Meta + plantillas WhatsApp, activad **SMS de venta digital** en el servidor (prioridad automática sobre WhatsApp):

```env
DIGITAL_SALE_SMS_ENABLED=true
DIGITAL_SALE_SMS_DRIVER=twilio
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_FROM=+15076099548
DIGITAL_SALE_SMS_DEFAULT_COUNTRY_CODE=34
WHATSAPP_ENABLED=false
```

- `TWILIO_FROM` = número SMS de Twilio (E.164, **sin** prefijo `whatsapp:`).
- Mismo mensaje (código + enlace), **texto libre**, sin plantilla Meta.
- Cuenta Trial: límite diario de mensajes (SMS + WhatsApp suman al mismo cupo).
- Desarrollo sin gastar SMS: `DIGITAL_SALE_SMS_DRIVER=log` (mensaje en `storage/logs/laravel.log`).

La app usa `buyer_notify_channel`: `sms` > `whatsapp` > manual (`wa.me`).

---

*Documento generado para el proyecto Partilot. La integración en app (historial, venta sin email) y API ya está implementada; WhatsApp depende de Meta/plantilla; SMS solo de Twilio + `TWILIO_FROM`.*
