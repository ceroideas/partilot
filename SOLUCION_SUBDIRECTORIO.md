# 🔧 Solución para Firebase en Subdirectorio

## 📍 Tu Configuración

- **Dominio:** https://ceroideas.es
- **Ruta de la app:** `/partilot/public/`
- **URL completa:** https://ceroideas.es/partilot/public/

---

## ✅ SOLUCIÓN APLICADA

He configurado el sistema para que detecte automáticamente el subdirectorio y ajuste todas las rutas correctamente.

### Archivos Actualizados:

1. ✅ `public/js/firebase-notifications.js` - Detecta ruta base automáticamente
2. ✅ `public/fix-firebase.html` - Herramienta de reparación para subdirectorios
3. ✅ `public/register-sw.html` - Herramienta de registro para subdirectorios
4. ✅ `public/firebase-messaging-sw.js` - Ya estaba correcto

---

## 🚀 PASOS PARA PROBAR

### 1. Limpia los Service Workers Viejos

Abre la consola del navegador (F12) y ejecuta:

```javascript
navigator.serviceWorker.getRegistrations().then(function(registrations) {
    for(let registration of registrations) {
        registration.unregister();
        console.log('Desregistrado:', registration.scope);
    }
    console.log('✅ Limpieza completada. Recarga la página.');
});
```

**Luego recarga la página (F5)**

---

### 2. Usa la Herramienta de Reparación Automática

**Abre:**
```
https://ceroideas.es/partilot/public/fix-firebase.html
```

**Haz clic en: "🔧 Reparar Todo"**

La herramienta ahora:
- ✅ Detecta automáticamente que estás en `/partilot/public/`
- ✅ Registra el Service Worker con la ruta correcta
- ✅ Configura todo el sistema de notificaciones

---

### 3. Verifica en los Logs

Cuando la herramienta se ejecute, deberías ver:

```
Ruta base detectada: /partilot/public
URL del SW: /partilot/public/firebase-messaging-sw.js
Scope: /partilot/public/
✅ Service Worker registrado y activo
Scope real: https://ceroideas.es/partilot/public/
```

---

## 🔍 Verificación Manual

Si quieres verificar que todo está correcto:

### 1. Abre DevTools (F12) → Application → Service Workers

Deberías ver:
```
Source: /partilot/public/firebase-messaging-sw.js
Scope: https://ceroideas.es/partilot/public/
Status: activated and is running
```

### 2. Verifica en la Consola

Recarga cualquier página del dashboard y busca en la consola:

```
🔍 Base path detectado: /partilot/public
🔍 Registrando Service Worker...
   URL: /partilot/public/firebase-messaging-sw.js
   Scope: /partilot/public/
✅ Service Worker registrado: https://ceroideas.es/partilot/public/
```

---

## 📱 Prueba de Notificaciones

### Desde el Dashboard:

1. Ve a: `https://ceroideas.es/partilot/public/notifications/dashboard`
2. Haz clic en "Enviar Prueba"
3. **IMPORTANTE:** Cambia a otra pestaña o minimiza el navegador
4. Deberías recibir la notificación

### ¿Por qué cambiar de pestaña?

Las notificaciones push de Firebase solo se muestran cuando:
- La app está en **segundo plano** (background)
- O la pestaña **no está enfocada**

Si la pestaña está activa, Firebase asume que el usuario ya está viendo la app y no muestra la notificación.

---

## 🎯 Si Aún No Funciona

### Revisa los Permisos del Navegador

1. Haz clic en el **candado** 🔒 en la barra de direcciones
2. Ve a "Configuración del sitio" o "Site settings"
3. Busca "Notificaciones"
4. Asegúrate de que esté en **"Permitir"**

### Verifica el Service Worker

En DevTools → Application → Service Workers:

- ✅ Debe decir "activated and is running"
- ✅ El Scope debe ser: `https://ceroideas.es/partilot/public/`
- ❌ Si dice "redundant" o "stopped", desregístralo y vuelve a usar la herramienta

### Revisa la Consola

Busca errores en la consola (F12 → Console):

- ❌ Si ves "404" → El Service Worker no se encuentra
- ❌ Si ves "SecurityError" → Problema con HTTPS o permisos
- ❌ Si ves "Failed to register" → La ruta es incorrecta

---

## 🔄 Si Necesitas Empezar de Cero

### Script de Limpieza Completa

Abre la consola (F12) y ejecuta:

```javascript
// 1. Desregistrar todos los Service Workers
navigator.serviceWorker.getRegistrations().then(function(registrations) {
    for(let registration of registrations) {
        registration.unregister();
    }
});

// 2. Limpiar caché
caches.keys().then(function(names) {
    for (let name of names) {
        caches.delete(name);
    }
});

// 3. Limpiar permisos (no funciona en todos los navegadores)
// Debes hacerlo manualmente en la configuración del navegador

console.log('✅ Limpieza completada. Recarga la página (F5)');
```

**Luego:**
1. Cierra y abre el navegador
2. Ve a: `https://ceroideas.es/partilot/public/fix-firebase.html`
3. Haz clic en "🔧 Reparar Todo"

---

## 📊 Estado Esperado del Sistema

Después de la reparación:

```
✅ Service Worker: ACTIVO
   Scope: https://ceroideas.es/partilot/public/
   
✅ Permisos: CONCEDIDOS
   Notifications: Allow
   
✅ Token FCM: REGISTRADO
   Token: drWvpww6ERAYaDT2VS0PMd:APA91bF...
   
✅ Backend: FUNCIONANDO
   Laravel logs: ✅ Notificación enviada exitosamente
```

---

## 💡 Notas Importantes

### Sobre HTTPS
- ✅ Tu sitio usa HTTPS (ceroideas.es)
- ✅ Firebase requiere HTTPS para funcionar
- ✅ Service Workers requieren HTTPS (excepto localhost)

### Sobre Subdirectorios
- ✅ El sistema ahora detecta automáticamente el subdirectorio
- ✅ Funciona en cualquier ruta: `/app/`, `/proyecto/public/`, etc.
- ✅ También funciona en raíz `/`

### Sobre Navegadores
- ✅ Chrome/Edge: Completamente soportado
- ✅ Firefox: Completamente soportado
- ⚠️ Safari: Requiere iOS 16.4+ / macOS 13+
- ❌ Internet Explorer: No soportado

---

## 🆘 Soporte

Si después de seguir todos estos pasos aún no funciona:

1. **Revisa los logs de Laravel:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Revisa la consola del navegador (F12)**

3. **Ejecuta el diagnóstico:**
   ```bash
   php artisan firebase:diagnose
   ```

4. **Verifica que la API de Firebase esté habilitada:**
   - https://console.cloud.google.com/apis/library/fcm.googleapis.com

---

**Última actualización:** 2025-10-19  
**Configuración probada en:** https://ceroideas.es/partilot/public/

