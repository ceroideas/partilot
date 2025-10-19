# 🔴 Solución para Errores de Firebase

## Errores Detectados

1. ❌ `invalid_grant` - Credenciales de cuenta de servicio inválidas
2. ❌ Error 404 - Server Key de API Legacy inválido

---

## ✅ SOLUCIÓN DEFINITIVA (Paso a Paso)

### 🔥 PASO 1: Habilitar Firebase Cloud Messaging API (CRÍTICO)

1. Abre este enlace:
   ```
   https://console.cloud.google.com/apis/library/fcm.googleapis.com?project=inicio-de-sesion-94ddc
   ```

2. Haz clic en el botón azul **"ENABLE"**

3. Espera unos segundos a que se active

**¿Por qué?** Sin esta API habilitada, Firebase no puede enviar notificaciones.

---

### 👤 PASO 2: Añadir Permisos a la Cuenta de Servicio

1. Abre este enlace:
   ```
   https://console.cloud.google.com/iam-admin/iam?project=inicio-de-sesion-94ddc
   ```

2. Busca en la lista:
   ```
   partilot@inicio-de-sesion-94ddc.iam.gserviceaccount.com
   ```

3. Haz clic en el icono de **lápiz** (EDIT) en esa fila

4. Haz clic en **"ADD ANOTHER ROLE"**

5. En el buscador, escribe: **"Firebase Admin"**

6. Selecciona **"Firebase Admin SDK Administrator Service Agent"**

7. Haz clic en **"SAVE"**

**¿Por qué?** La cuenta de servicio necesita permisos para enviar notificaciones.

---

### 🔑 PASO 3: Regenerar Credenciales (Service Account)

1. Abre este enlace:
   ```
   https://console.firebase.google.com/project/inicio-de-sesion-94ddc/settings/serviceaccounts/adminsdk
   ```

2. Verás una sección que dice **"Admin SDK configuration snippet"**

3. Haz clic en el botón **"Generate new private key"** (botón azul)

4. Confirma haciendo clic en **"Generate key"**

5. Se descargará un archivo JSON (algo como `inicio-de-sesion-94ddc-firebase-adminsdk-xxxxx.json`)

6. **Renombra** ese archivo a: `firebase-credentials.json`

7. **Reemplaza** el archivo existente en:
   ```
   C:\xampp3\htdocs\sipart\storage\firebase-credentials.json
   ```

**¿Por qué?** Las credenciales antiguas pueden estar expiradas o corruptas.

---

### 🔐 PASO 4: Obtener Server Key (API Legacy - Opcional)

1. Abre este enlace:
   ```
   https://console.firebase.google.com/project/inicio-de-sesion-94ddc/settings/cloudmessaging
   ```

2. Busca la sección **"Cloud Messaging API (Legacy)"**

3. **Si la ves:**
   - Copia el **"Server key"** (comienza con `AAAA...`)
   - Actualiza tu archivo `.env`:
     ```env
     FIREBASE_SERVER_KEY=tu_nuevo_server_key_aqui
     ```

4. **Si NO la ves:**
   - No te preocupes, usaremos solo la API V1 (moderna)
   - Puedes comentar o eliminar `FIREBASE_SERVER_KEY` del `.env`

---

### ✅ PASO 5: Verificar que Todo Funciona

1. Abre tu terminal en la carpeta del proyecto

2. Ejecuta:
   ```bash
   php artisan firebase:diagnose
   ```

3. Cuando pregunte si quieres enviar una notificación de prueba, escribe: `yes`

4. **Si funciona:**
   - ✅ Verás "Notificación enviada exitosamente"
   - ✅ Recibirás la notificación en tu navegador

5. **Si aún falla:**
   - Revisa el archivo `storage/logs/laravel.log`
   - Busca el error más reciente

---

## 🎯 Verificación Final

Una vez completados los pasos, ejecuta:

```bash
php artisan firebase:test --send-test
```

Deberías ver:
```
✅ Notificación de prueba enviada exitosamente
```

Y recibir una notificación en tu navegador.

---

## 🐛 Si Aún No Funciona

### Limpia la caché de Laravel:
```bash
php artisan config:clear
php artisan cache:clear
```

### Verifica los logs:
```bash
tail -f storage/logs/laravel.log
```

### Verifica que el token FCM del usuario sea válido:
- Abre la consola del navegador (F12)
- Busca "FCM Token:" en los logs
- Debe aparecer un token largo

---

## 📞 Checklist Final

- [ ] Firebase Cloud Messaging API habilitada
- [ ] Cuenta de servicio tiene rol "Firebase Admin"
- [ ] Credenciales regeneradas y reemplazadas
- [ ] Server Key actualizado (si aplica)
- [ ] Comando `php artisan firebase:diagnose` ejecutado
- [ ] Notificación de prueba recibida

---

## ⏱️ Tiempo Estimado

- Paso 1-4: **5-10 minutos**
- Paso 5: **2 minutos**

**Total: ~10-15 minutos**

---

¡Una vez completados estos pasos, tu sistema de notificaciones debería funcionar perfectamente! 🎉

