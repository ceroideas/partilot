# Programador Laravel (`schedule`) y etiquetas `kind` en la app

## Cron del servidor

Laravel no ejecuta tareas solas: hay que definirlas en `app/Console/Kernel.php` y en el servidor:

```cron
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

En Windows/XAMPP suele usarse el Programador de tareas llamando a `php artisan schedule:run` cada minuto.

## Comandos Artisan stub (implementar luego)

| Comando | Propósito |
|--------|-----------|
| `php artisan sipart:pending-payments-check` | Pagos pendientes / reconciliación |
| `php artisan sipart:lottery-deadline-reminder` | Avisos 3/2/1/0 días (email + modal) |
| `php artisan sipart:lottery-deadline-closure` | Cierre automático tras fecha límite (vendidas + deuda) |
| `php artisan sipart:new-lotteries-announce` | Avisar sorteos nuevos en catálogo |

En `Kernel::schedule()`: recordatorios 09:00; cierre 00:30 solo si `LOTTERY_AUTO_DEADLINE_CLOSURE_ENABLED=true`.

## Valores `kind` en BD (backend → campo API `tipo`)

Referencia para la app móvil (`notification-kind.labels.ts`):

| `kind` | Origen típico |
|--------|----------------|
| `manual_entidad` | Panel: notificación por entidad |
| `push_directo_panel` | Panel: push directo a usuario (también crea fila inbox) |
| `regalo_participacion` | API regalo entre usuarios |
| `cobro_registrado` | API registro de cobro por transferencia |
| `invitacion_vendedor` | Alta vendedor PARTILOT con usuario existente |
| `asignacion_participaciones` | Job asignación a vendedor |
| `resultados_sorteo` | Job tras publicar/actualizar resultados |
| `manual` | Fallback si `kind` es null |

La API serializa `notifications.kind` como JSON `tipo` (ver `NotificationController::formatNotificationForApp`).

## App Ionic

- Mapa legible: `src/app/core/constants/notification-kind.labels.ts`
- Normalización para iconos/navegación: `normalizeNotificationTipoForUi()`


Aquí tienes lo que quedó montado:

Laravel (h:/xampp3/htdocs/sipart)
Comandos Artisan (namespace sipart:*, solo mensajes guía por ahora):

sipart:pending-payments-check (--dry-run)
sipart:lottery-deadline-reminder (--dry-run)
sipart:new-lotteries-announce (--dry-run)
app/Console/Kernel.php
Comentarios con ejemplos de schedule y referencia al doc (descomenta cuando implementes y tengas cron de schedule:run).

docs/SCHEDULER_AND_KIND_LABELS.md
Instrucciones de cron, tabla de kind backend ↔ API tipo, y referencia al archivo de etiquetas en la app.

Comprobado: php artisan list sipart lista los tres comandos.

App Ionic (h:/Users/Jorge/proyectos/sipart)
src/app/core/constants/notification-kind.labels.ts

NOTIFICATION_KIND_LABELS + notificationKindLabel()
normalizeNotificationTipoForUi() para enlazar cobro_registrado → cobros, regalo_participacion → regalo, resultados_sorteo → sorteo.
Listado (notificaciones): pill pill-kind con la etiqueta del tipo.

Detalle (notificacion-detalle): línea con la misma etiqueta; iconos y botones usan tipoUi (normalizado).

Cuando implementes lógica real en los comandos, descomenta las líneas en Kernel y configura el cron del servidor (o el Programador de tareas en Windows) como indica el markdown.