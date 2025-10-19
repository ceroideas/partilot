# Guía de Pruebas y Solución de Problemas - Firebase Notifications

## Cambios Realizados

### 1. Soporte para Subcarpetas
- ✅ Detección automática de la ruta base (`/partilot/public/`)
- ✅ Service Worker registrado con scope correcto
- ✅ Todas las rutas del backend ajustadas

### 2. Mejoras en el Manejo de Errores
- ✅ Logs detallados para registro de tokens
- ✅ Validación de CSRF token
- ✅ Mensajes de error claros en consola

### 3. Base de Datos
- ✅ Campo `fcm_token` añadido a la tabla `users`

## Pasos para Probar

### 1. Limpiar Caché y Recargar
```bash
# Limpiar service workers antiguos
1. Abre DevTools (F12)
2. Ve a Application > Service Workers
3. Haz clic en "Unregister" en todos los service workers antiguos
4. Recarga la página con Ctrl+Shift+R
```

### 2. Verificar Registro del Token
Abre la consola del navegador y busca estos mensajes:

```javascript
✅ "Registering service worker: /partilot/public/firebase-messaging-sw.js"
✅ "Service Worker registered: [ServiceWorkerRegistration]"
✅ "Notification permission granted"
✅ "FCM Token: [tu-token]"
✅ "Token successfully registered on server" // ← Este es el mensaje clave
✅ "Firebase Notifications initialized successfully"
```

Si ves algún error, especialmente:
- ❌ "CSRF token not found" → Verifica que el layout tenga `<meta name="csrf-token">`
- ❌ "Failed to register token on server: 401" → Problema de autenticación
- ❌ "Failed to register token on server: 419" → Token CSRF expirado

### 3. Verificar Token en Base de Datos

```bash
php artisan tinker
```

```php
// Ver el token del usuario actual (asumiendo que eres el usuario con ID 1)
$user = User::find(1);
echo "Token: " . $user->fcm_token . "\n";
```

El token debe coincidir con el mostrado en la consola del navegador.

### 4. Enviar Notificación de Prueba desde Firebase Console

#### Opción A: Enviar a Token Específico (RECOMENDADO)
1. Ve a Firebase Console → Cloud Messaging
2. Haz clic en "Send your first message" o "New campaign"
3. Título: "Prueba de notificación"
4. Mensaje: "Esta es una prueba"
5. En "Target", selecciona "FCM registration token"
6. Pega el token FCM de la consola del navegador
7. Haz clic en "Test" o "Send"

#### Opción B: Enviar a Todos los Usuarios de la App
1. Ve a Firebase Console → Cloud Messaging
2. En "Target", selecciona "User segment" → "Partilot"
3. IMPORTANTE: Esto solo funcionará si hay tokens registrados en Firebase

### 5. Probar Notificaciones en Diferentes Estados

#### A. Página Abierta (Foreground)
- Deberías ver una notificación tipo "toast" en la página
- Y una notificación del sistema operativo

#### B. Página en Segundo Plano (Background)
- Abre la página
- Cambia a otra pestaña o minimiza el navegador
- Envía notificación desde Firebase Console
- Deberías recibir una notificación del sistema operativo

#### C. Navegador Cerrado
- IMPORTANTE: El navegador debe estar cerrado COMPLETAMENTE
- Las notificaciones NO llegarán si el navegador está cerrado
- Esto es una limitación de las PWA en navegadores web

## Solución de Problemas Comunes

### Problema 1: Token No Se Guarda
**Síntomas:** El token aparece en consola pero no en la base de datos

**Solución:**
1. Verifica que el usuario esté autenticado
2. Verifica que exista el meta tag CSRF en el layout
3. Revisa los logs del servidor:
```bash
tail -f storage/logs/laravel.log
```

### Problema 2: Notificaciones No Llegan
**Síntomas:** Token guardado correctamente pero no llegan notificaciones

**Posibles causas:**

#### A. Service Worker No Activo
```javascript
// En la consola del navegador:
navigator.serviceWorker.getRegistrations().then(registrations => {
    registrations.forEach(reg => {
        console.log('SW:', reg.scope, 'Active:', reg.active);
    });
});
```

#### B. Token Expirado o Inválido
Los tokens FCM pueden expirar. Solución:
1. Limpia el service worker
2. Borra el token de la base de datos
3. Recarga la página
4. Se generará un nuevo token

#### C. Servidor Key Incorrecto
Verifica en `.env`:
```
FIREBASE_SERVER_KEY=tu-server-key-aqui
```

Obtén la Server Key de:
Firebase Console → Project Settings → Cloud Messaging → Server key (legacy)

### Problema 3: Error 404 en Service Worker
**Síntomas:** `GET /firebase-messaging-sw.js 404`

**Solución:**
Ya está solucionado con la ruta en `web.php`, pero si persiste:
1. Verifica que `public/firebase-messaging-sw.js` existe
2. Verifica permisos del archivo
3. Limpia caché de Laravel:
```bash
php artisan cache:clear
php artisan route:clear
php artisan config:clear
```

## Verificación de Configuración

### 1. Archivo .env
```bash
FIREBASE_API_KEY=AIzaSyABsAHy3BtYUkcV4z3gjCl3NNU35ye4LFs
FIREBASE_AUTH_DOMAIN=inicio-de-sesion-94ddc.firebaseapp.com
FIREBASE_DATABASE_URL=https://inicio-de-sesion-94ddc.firebaseio.com
FIREBASE_PROJECT_ID=inicio-de-sesion-94ddc
FIREBASE_STORAGE_BUCKET=inicio-de-sesion-94ddc.firebasestorage.app
FIREBASE_MESSAGING_SENDER_ID=204683025370
FIREBASE_APP_ID=1:204683025370:web:c424b261eff8d566be7ee3
FIREBASE_SERVER_KEY=[TU_SERVER_KEY_AQUI]
```

### 2. Verificar VAPID Key
La VAPID key en `firebase-notifications.js` debe coincidir con la de Firebase Console:
```javascript
vapidKey: 'BLM73awUlpn-eZx9osSf_usO1PYU93Eb2FjV37RoYivoBIdA1jRirM7ErlwE6pyLU-jYhe9TnhfUYM2YRiqQ58U'
```

Obtén tu VAPID key de:
Firebase Console → Project Settings → Cloud Messaging → Web Push certificates

## Debugging Avanzado

### Ver Mensajes del Service Worker
```javascript
// En DevTools → Application → Service Workers → firebase-messaging-sw.js
// Haz clic en "inspect" para ver la consola del service worker
```

### Forzar Actualización del Service Worker
```javascript
// En la consola del navegador:
navigator.serviceWorker.getRegistrations().then(registrations => {
    registrations.forEach(reg => reg.update());
});
```

### Ver Todos los Tokens en la Base de Datos
```bash
php artisan tinker
```
```php
User::whereNotNull('fcm_token')->get(['id', 'name', 'email', 'fcm_token']);
```

## Próximos Pasos

Una vez que las notificaciones funcionen correctamente:

1. **Implementar Envío desde Backend:**
   - Usar `FirebaseService` para enviar notificaciones programáticas
   - Ejemplo: cuando se crea una nueva participación

2. **Gestión de Topics:**
   - Suscribir usuarios a topics específicos (por entidad, administración, etc.)
   - Enviar notificaciones masivas por topic

3. **Notificaciones Personalizadas:**
   - Añadir datos personalizados en el payload
   - Manejar acciones específicas al hacer clic

4. **PWA Completa:**
   - Añadir manifest.json
   - Configurar iconos para diferentes tamaños
   - Habilitar instalación de la app

## Contacto y Soporte

Si después de seguir todos estos pasos aún no funcionan las notificaciones, proporciona:
1. Screenshots de la consola del navegador
2. Logs de Laravel (`storage/logs/laravel.log`)
3. El token FCM que aparece en consola
4. El estado del service worker (activo/instalando/esperando)

