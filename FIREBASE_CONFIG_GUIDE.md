# 🔥 Guía de Configuración de Firebase Push Notifications

## 📋 Estado Actual

### ✅ Componentes Implementados

1. **Backend (Laravel)**
   - ✅ `FirebaseService.php` - API Legacy de Firebase
   - ✅ `FirebaseServiceModern.php` - API V1 de Firebase (actualizada)
   - ✅ `NotificationController.php` - Controlador para gestionar notificaciones
   - ✅ Modelo `Notification` con relaciones
   - ✅ Campo `fcm_token` en tabla `users`
   - ✅ Rutas configuradas

2. **Frontend**
   - ✅ `firebase-notifications.js` - Cliente de Firebase
   - ✅ `firebase-messaging-sw.js` - Service Worker
   - ✅ Integración en `layout.blade.php`

3. **Configuración**
   - ✅ Credenciales en `storage/firebase-credentials.json`
   - ✅ Archivo de configuración `config/firebase.php`
   - ✅ Librería `kreait/firebase-php` v7.0 instalada

---

## 🔧 Pasos de Configuración

### 1. Configurar Variables de Entorno (.env)

Agrega estas variables en tu archivo `.env`:

```env
# Firebase Configuration
FIREBASE_API_KEY=AIzaSyABsAHy3BtYUkcV4z3gjCl3NNU35ye4LFs
FIREBASE_AUTH_DOMAIN=inicio-de-sesion-94ddc.firebaseapp.com
FIREBASE_DATABASE_URL=https://inicio-de-sesion-94ddc.firebaseio.com
FIREBASE_PROJECT_ID=inicio-de-sesion-94ddc
FIREBASE_STORAGE_BUCKET=inicio-de-sesion-94ddc.firebasestorage.app
FIREBASE_MESSAGING_SENDER_ID=204683025370
FIREBASE_APP_ID=1:204683025370:web:c424b261eff8d566be7ee3

# Firebase Server Key (Legacy API - necesario para API Legacy)
# Ve a Firebase Console → Project Settings → Cloud Messaging → Server Key
FIREBASE_SERVER_KEY=tu_server_key_aqui
```

### 2. Obtener el Server Key de Firebase

1. Ve a [Firebase Console](https://console.firebase.google.com/)
2. Selecciona tu proyecto: **inicio-de-sesion-94ddc**
3. Ve a **⚙️ Project Settings** → **Cloud Messaging**
4. En la sección "**Cloud Messaging API (Legacy)**", copia el **Server key**
5. Pégalo en tu `.env` como valor de `FIREBASE_SERVER_KEY`

> ⚠️ **Nota**: La API Legacy se usará como fallback si la API V1 falla.

### 3. Verificar Permisos de la Cuenta de Servicio

Tu cuenta de servicio necesita permisos adecuados en Google Cloud:

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Selecciona el proyecto **inicio-de-sesion-94ddc**
3. Ve a **IAM & Admin** → **IAM**
4. Busca: `partilot@inicio-de-sesion-94ddc.iam.gserviceaccount.com`
5. Asegúrate de que tenga estos roles:
   - ✅ **Firebase Admin SDK Administrator Service Agent**
   - ✅ **Cloud Messaging Admin** (o mínimo **Editor**)

Si no tiene estos roles:
- Haz clic en **EDIT** (icono de lápiz)
- Agrega los roles faltantes
- Guarda los cambios

### 4. Habilitar Firebase Cloud Messaging API

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Selecciona tu proyecto
3. Ve a **APIs & Services** → **Library**
4. Busca "**Firebase Cloud Messaging API**"
5. Si no está habilitado, haz clic en **ENABLE**

### 5. Verificar el VAPID Key

El VAPID key ya está configurado en `firebase-notifications.js`:

```javascript
vapidKey: 'BLM73awUlpn-eZx9osSf_usO1PYU93Eb2FjV37RoYivoBIdA1jRirM7ErlwE6pyLU-jYhe9TnhfUYM2YRiqQ58U'
```

Si necesitas generar uno nuevo:

1. Ve a Firebase Console → Project Settings → Cloud Messaging
2. En la sección **Web Push certificates**, haz clic en **Generate key pair**
3. Copia el nuevo key y reemplázalo en `firebase-notifications.js`

---

## 🧪 Probar la Configuración

### Comando de Prueba

Ejecuta el comando de Artisan para verificar la configuración:

```bash
php artisan firebase:test
```

Este comando verificará:
- ✅ Archivo de credenciales
- ✅ Variables de entorno
- ✅ Conexión con Firebase
- ✅ Usuarios con tokens FCM

### Enviar Notificación de Prueba

```bash
php artisan firebase:test --send-test
```

Este comando enviará una notificación de prueba a todos los usuarios registrados.

---

## 📱 Cómo Funcionan las Notificaciones

### Flujo de Trabajo

1. **Registro del Usuario**
   - El usuario abre la aplicación web
   - `firebase-notifications.js` solicita permiso para notificaciones
   - Si se concede, obtiene un token FCM
   - El token se guarda en la base de datos (`users.fcm_token`)

2. **Envío de Notificaciones**
   - Un admin crea una notificación en `/notifications/create`
   - Selecciona entidad o administración
   - Escribe título y mensaje
   - El sistema envía la notificación a través de Firebase

3. **Recepción**
   - **Aplicación abierta**: La notificación aparece como toast
   - **Aplicación cerrada**: El Service Worker muestra la notificación del sistema

---

## 🐛 Solución de Problemas

### No se reciben notificaciones

1. **Verificar permisos del navegador**
   - Abre la consola del navegador (F12)
   - Ve a Application → Permissions
   - Asegúrate de que "Notifications" esté en "Allowed"

2. **Revisar logs de Laravel**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Verificar Service Worker**
   - Abre DevTools → Application → Service Workers
   - Debe mostrar `firebase-messaging-sw.js` como "activated"

4. **Verificar token FCM**
   - Abre la consola del navegador
   - Busca "FCM Token:" en los logs
   - Verifica que el token se haya guardado en la base de datos

### Error de autenticación

Si ves errores de autenticación:

1. Verifica que el archivo `storage/firebase-credentials.json` exista
2. Revisa que la cuenta de servicio tenga los permisos correctos
3. Asegúrate de que la API de Cloud Messaging esté habilitada

### Error 404 en Service Worker

Si el Service Worker no se carga:

1. Verifica que `public/firebase-messaging-sw.js` exista
2. Asegúrate de que la ruta base sea correcta en `firebase-notifications.js`
3. Limpia la caché del navegador

---

## 📚 Recursos

- [Firebase Console](https://console.firebase.google.com/)
- [Google Cloud Console](https://console.cloud.google.com/)
- [Firebase PHP SDK Docs](https://firebase-php.readthedocs.io/)
- [Firebase JS SDK Docs](https://firebase.google.com/docs/cloud-messaging/js/client)

---

## 🎯 Próximos Pasos Recomendados

1. ✅ Configurar variables de entorno
2. ✅ Verificar permisos de cuenta de servicio
3. ✅ Ejecutar `php artisan firebase:test`
4. ✅ Enviar notificación de prueba
5. ⭐ Configurar notificaciones por entidad específica (opcional)
6. ⭐ Implementar notificaciones programadas (opcional)
7. ⭐ Agregar badges de notificaciones no leídas (opcional)

---

**¿Necesitas ayuda?** Revisa los logs en `storage/logs/laravel.log` para más detalles.


