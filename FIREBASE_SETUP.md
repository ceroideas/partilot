# Configuración de Firebase para Notificaciones

## 1. Variables de Entorno

Agrega las siguientes variables a tu archivo `.env`:

```env
# Firebase Configuration
FIREBASE_API_KEY=AIzaSyABsAHy3BtYUkcV4z3gjCl3NNU35ye4LFs
FIREBASE_AUTH_DOMAIN=inicio-de-sesion-94ddc.firebaseapp.com
FIREBASE_DATABASE_URL=https://inicio-de-sesion-94ddc.firebaseio.com
FIREBASE_PROJECT_ID=inicio-de-sesion-94ddc
FIREBASE_STORAGE_BUCKET=inicio-de-sesion-94ddc.firebasestorage.app
FIREBASE_MESSAGING_SENDER_ID=204683025370
FIREBASE_APP_ID=1:204683025370:web:c424b261eff8d566be7ee3

# Firebase Server Key (obtén esto de Firebase Console > Project Settings > Cloud Messaging)
FIREBASE_SERVER_KEY=your_server_key_here
```

## 2. Obtener Firebase Server Key

1. Ve a [Firebase Console](https://console.firebase.google.com/)
2. Selecciona tu proyecto: `inicio-de-sesion-94ddc`
3. Ve a **Project Settings** (icono de engranaje)
4. Ve a la pestaña **Cloud Messaging**
5. Copia el **Server Key** y pégalo en `FIREBASE_SERVER_KEY` en tu `.env`

## 3. Configurar Service Worker

Crea el archivo `public/firebase-messaging-sw.js`:

```javascript
// Import Firebase scripts
importScripts('https://www.gstatic.com/firebasejs/12.4.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/12.4.0/firebase-messaging-compat.js');

// Initialize Firebase
firebase.initializeApp({
    apiKey: "AIzaSyABsAHy3BtYUkcV4z3gjCl3NNU35ye4LFs",
    authDomain: "inicio-de-sesion-94ddc.firebaseapp.com",
    databaseURL: "https://inicio-de-sesion-94ddc.firebaseio.com",
    projectId: "inicio-de-sesion-94ddc",
    storageBucket: "inicio-de-sesion-94ddc.firebasestorage.app",
    messagingSenderId: "204683025370",
    appId: "1:204683025370:web:c424b261eff8d566be7ee3"
});

// Initialize Firebase Messaging
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('Received background message ', payload);
    
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/favicon.ico',
        badge: '/favicon.ico'
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
```

## 4. Configurar VAPID Key

1. En Firebase Console, ve a **Project Settings** > **Cloud Messaging**
2. En la sección **Web configuration**, genera un **Web Push certificate**
3. Copia la **Key pair** y úsala en el archivo `public/js/firebase-notifications.js`

## 5. Probar las Notificaciones

1. Asegúrate de que el servidor esté ejecutándose
2. Ve a `/notifications` en tu aplicación
3. Crea una nueva notificación
4. Las notificaciones push se enviarán automáticamente

## 6. Funcionalidades Implementadas

- ✅ Envío de notificaciones push a entidades específicas
- ✅ Envío de notificaciones push a administraciones
- ✅ Notificaciones en tiempo real
- ✅ Manejo de tokens FCM
- ✅ Notificaciones en primer plano y segundo plano
- ✅ Toast notifications
- ✅ Integración completa con el sistema de notificaciones

## 7. Rutas Disponibles

- `GET /notifications/firebase-config` - Obtener configuración de Firebase
- `POST /notifications/register-token` - Registrar token FCM del usuario
- `GET /notifications` - Lista de notificaciones
- `POST /notifications/store` - Enviar notificación (con Firebase)

## 8. Archivos Creados

- `config/firebase.php` - Configuración de Firebase
- `app/Services/FirebaseService.php` - Servicio de Firebase
- `public/js/firebase-notifications.js` - JavaScript para notificaciones
- `app/Http/Controllers/NotificationController.php` - Controlador actualizado
- `resources/views/layouts/layout.blade.php` - Layout actualizado
