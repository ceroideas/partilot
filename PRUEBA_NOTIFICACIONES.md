# 🔔 Prueba de Notificaciones Firebase - Instrucciones Rápidas

## ✅ Cambios Implementados

- **Envío a todos los usuarios**: Ahora las notificaciones se envían a TODOS los usuarios con tokens FCM, independientemente de la selección de entidades o administraciones
- **Logs mejorados**: Información detallada en `storage/logs/laravel.log`
- **Vista de éxito mejorada**: Muestra cuántos dispositivos recibieron la notificación

## 📋 Pasos para Probar

### 1. Verificar Token Registrado

Abre la aplicación y verifica en la consola del navegador (F12):
```
✅ "Token successfully registered on server"
```

Si NO ves este mensaje, recarga con `Ctrl+Shift+R` y revisa si hay errores.

### 2. Enviar Notificación de Prueba

1. Ve a: **Notificaciones → Nueva Notificación**
2. Selecciona cualquier entidad/administración (da igual, se enviará a todos)
3. Escribe:
   - **Título**: Prueba de notificación
   - **Mensaje**: Esta es una prueba desde Partilot
4. Haz clic en **Enviar**

### 3. Verificar Resultado

Deberías ver en la página de éxito:
```
✉️ Notificaciones guardadas: X destinatario(s)
📱 Notificaciones push enviadas: Y dispositivo(s)
```

### 4. Verificar Logs

Abre los logs de Laravel para ver el proceso completo:

```bash
# En Windows (PowerShell)
Get-Content storage\logs\laravel.log -Tail 50

# O abre el archivo directamente
notepad storage\logs\laravel.log
```

Busca estas líneas:
```
=== ENVIANDO NOTIFICACIÓN FIREBASE A TODOS LOS USUARIOS ===
Usuarios con tokens FCM: X
📤 Enviando notificación Firebase
📥 Respuesta de Firebase
✅ Notificación Firebase enviada exitosamente
```

## 🐛 Si No Funciona

### Problema: "No hay usuarios con tokens FCM registrados"

**Solución:**
1. Abre la aplicación en el navegador
2. F12 → Consola
3. Busca: `Token successfully registered on server`
4. Si NO aparece, verifica:
   ```bash
   php artisan tinker
   User::find(TU_ID)->fcm_token
   ```

### Problema: "Firebase Server Key no configurado"

**Solución:**
1. Edita tu archivo `.env`
2. Agrega (o verifica):
   ```
   FIREBASE_SERVER_KEY=AAAAtu_server_key_aquí
   ```
3. Obtén el Server Key de:
   - Firebase Console → Project Settings → Cloud Messaging → Server key (legacy)
4. Limpia configuración:
   ```bash
   php artisan config:clear
   ```

### Problema: Token se registra pero notificación no llega

**Verifica en los logs:**

1. **❌ Error 401 - Authentication Error:**
   - El Server Key es incorrecto
   - Verifica que copiaste el Server Key completo

2. **❌ InvalidRegistration:**
   - El token FCM es inválido
   - Borra el token de la BD y recarga la página:
   ```bash
   php artisan tinker
   User::find(TU_ID)->update(['fcm_token' => null])
   ```

3. **❌ NotRegistered o MismatchSenderId:**
   - El proyecto de Firebase no coincide
   - Verifica que el `messagingSenderId` en el código coincide con Firebase Console

## 🧪 Prueba con el Script

Ejecuta el script de prueba para verificar todo:

```bash
php test-firebase-notification.php
```

Este script:
1. Verifica la configuración de Firebase
2. Lista usuarios con tokens FCM
3. Envía una notificación de prueba al primer usuario
4. Muestra el resultado

## 📊 Verificar en la Base de Datos

```bash
php artisan tinker
```

```php
// Ver todos los usuarios con tokens
User::whereNotNull('fcm_token')->get(['id', 'name', 'email', 'fcm_token']);

// Ver el token de un usuario específico
$user = User::find(1);
echo "Token: " . substr($user->fcm_token, 0, 50) . "...";

// Actualizar manualmente si es necesario
$user->fcm_token = 'nuevo-token-aqui';
$user->save();
```

## 🎯 Estados de la Notificación

### Navegador Abierto (Foreground)
- ✅ Debería aparecer un toast en la página
- ✅ Notificación del sistema operativo

### Navegador en Segundo Plano (Background)
- ✅ Notificación del sistema operativo
- ✅ Se puede hacer clic para abrir la app

### Navegador Cerrado
- ❌ NO llegarán notificaciones
- Esto es normal en PWA web (solo apps nativas pueden recibir con navegador cerrado)

## 📱 Tipos de Notificaciones Según el Estado

| Estado del Navegador | ¿Llega Notificación? | Tipo |
|---------------------|----------------------|------|
| Pestaña activa | ✅ Sí | Toast + Sistema |
| Pestaña en segundo plano | ✅ Sí | Sistema |
| Navegador minimizado | ✅ Sí | Sistema |
| Navegador cerrado | ❌ No | - |

## 🔍 Debugging Avanzado

### Ver Service Worker Activo

En DevTools (F12):
1. Application → Service Workers
2. Verifica que esté "activated and is running"
3. Si está "waiting", haz clic en "skipWaiting"

### Ver Mensajes del Service Worker

1. Application → Service Workers
2. Haz clic en "inspect" junto al service worker
3. Se abrirá una consola específica del SW

### Forzar Actualización

```javascript
// En la consola del navegador
navigator.serviceWorker.getRegistrations().then(regs => {
    regs.forEach(reg => {
        console.log('SW:', reg.scope);
        reg.update();
    });
});
```

## ✨ Ejemplo de Log Exitoso

```
[2025-10-19 12:00:00] local.INFO: === ENVIANDO NOTIFICACIÓN FIREBASE A TODOS LOS USUARIOS ===  
[2025-10-19 12:00:00] local.INFO: Usuarios con tokens FCM: 1  
[2025-10-19 12:00:00] local.INFO: Tokens FCM: {"tokens":["dgwyQZ-oMA-295..."]}  
[2025-10-19 12:00:00] local.INFO: 📤 Enviando notificación Firebase {"destinatarios":1,"titulo":"Prueba"}  
[2025-10-19 12:00:01] local.INFO: 📥 Respuesta de Firebase: {"multicast_id":123,"success":1,"failure":0}  
[2025-10-19 12:00:01] local.INFO: ✅ Notificación Firebase enviada exitosamente {"exitosos":1,"fallidos":0}  
[2025-10-19 12:00:01] local.INFO: ✓ Notificación Firebase enviada exitosamente a 1 usuario(s)  
```

## 🎉 Siguiente Paso

Una vez que funcione correctamente:
1. Toma screenshot de la notificación llegando
2. Copia los logs exitosos
3. Confirma que todo está OK
4. Luego podemos implementar el envío específico por entidad/administración

## 📞 Información para Soporte

Si necesitas ayuda, proporciona:
1. ✅ Screenshot de la consola del navegador (con el token)
2. ✅ Últimas 100 líneas de `storage/logs/laravel.log`
3. ✅ Output del comando: `php test-firebase-notification.php`
4. ✅ Screenshot de la pantalla de éxito después de enviar

