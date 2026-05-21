# httpSMS — configuración Partilot

Pasarela SMS usando un móvil Android con línea telefónica ([documentación](https://docs.httpsms.com/)).

## Requisitos

1. Cuenta en [httpsms.com](https://httpsms.com/) y API key en [Ajustes](https://httpsms.com/settings).
2. App Android instalada y en línea: [HttpSms.apk](https://github.com/NdoleStudio/httpsms/releases/latest/download/HttpSms.apk).
3. Número del móvil en formato E.164 (`+34...`) como remitente.

## Variables `.env`

```env
HTTPSMS_API_KEY=tu_api_key
HTTPSMS_FROM_NUMBER=+34600111222

# Verificación OTP (registro)
SMS_VERIFICATION_ENABLED=true
SMS_DRIVER=httpsms

# Venta digital al comprador (vendedor → comprador)
DIGITAL_SALE_SMS_ENABLED=true
DIGITAL_SALE_SMS_DRIVER=httpsms
# Primer envío + reenvíos (1 = máximo 2 SMS por venta pendiente)
DIGITAL_SALE_SMS_MAX_RESENDS=1
```

Desarrollo sin móvil: `SMS_DRIVER=log` y `DIGITAL_SALE_SMS_DRIVER=log` (mensajes en `storage/logs/laravel.log`).

## Flujo venta digital

| Servidor | App vendedor |
|----------|----------------|
| SMS activo (httpSMS) | Envío automático por API al confirmar teléfono |
| SMS inactivo | Botón abre **wa.me** con mensaje (sin código en app) |

Twilio SMS y Twilio WhatsApp **no se usan** en este flujo.

## Prueba rápida (tinker)

```php
$user = \App\Models\User::find(1); // debe tener phone en E.164
$user?->notify(new \App\Notifications\TestSmsNotification());
```
