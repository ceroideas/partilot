# 🔧 Solución: Permisos de Firebase Cloud Messaging

## ❌ Problema Detectado

El error indica que la cuenta de servicio no tiene permisos para enviar notificaciones:

```
Permission 'cloudmessaging.messages.create' denied on resource '//cloudresourcemanager.googleapis.com/projects/inicio-de-sesion-94ddc'
```

## ✅ Solución

### Paso 1: Verificar la Cuenta de Servicio

1. Ve a [Firebase Console](https://console.firebase.google.com/)
2. Selecciona tu proyecto: `inicio-de-sesion-94ddc`
3. Ve a **Project Settings** (Configuración del proyecto)
4. Pestaña **Service Accounts**

### Paso 2: Verificar Permisos en Google Cloud Console

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Selecciona el proyecto: `inicio-de-sesion-94ddc`
3. Ve a **IAM & Admin** → **IAM**
4. Busca la cuenta de servicio: `partilot@inicio-de-sesion-94ddc.iam.gserviceaccount.com`

### Paso 3: Asignar Roles Necesarios

La cuenta de servicio necesita estos roles:

- **Firebase Cloud Messaging Admin** (`roles/firebasemessaging.admin`)
- **Firebase Admin** (`roles/firebase.admin`)
- **Cloud Messaging API** (`roles/cloudmessaging.admin`)

### Paso 4: Asignar Roles Manualmente

1. En Google Cloud Console → **IAM & Admin** → **IAM**
2. Haz clic en el ícono de editar (lápiz) junto a tu cuenta de servicio
3. Haz clic en **ADD ANOTHER ROLE**
4. Agrega estos roles:
   - `Firebase Cloud Messaging Admin`
   - `Firebase Admin`
   - `Cloud Messaging API`

### Paso 5: Alternativa - Regenerar Cuenta de Servicio

Si no puedes asignar permisos, regenera la cuenta de servicio:

1. Firebase Console → **Project Settings** → **Service Accounts**
2. Haz clic en **Generate new private key**
3. Descarga el nuevo archivo JSON
4. Reemplaza `storage/firebase-credentials.json` con el nuevo archivo

### Paso 6: Verificar APIs Habilitadas

Asegúrate de que estas APIs estén habilitadas:

1. Google Cloud Console → **APIs & Services** → **Enabled APIs**
2. Verifica que estén habilitadas:
   - Firebase Cloud Messaging API
   - Firebase Admin API
   - Cloud Resource Manager API

## 🧪 Probar la Solución

Después de aplicar los permisos, ejecuta:

```bash
php test-firebase-only.php
```

Deberías ver:
```
✅ Conexión exitosa (error esperado por token inválido)
```

## 📝 Notas Importantes

- Los cambios de permisos pueden tardar unos minutos en aplicarse
- Si usas una cuenta de servicio nueva, actualiza el archivo `storage/firebase-credentials.json`
- El archivo de credenciales debe estar en `.gitignore` por seguridad

## 🔗 Enlaces Útiles

- [Firebase Console](https://console.firebase.google.com/)
- [Google Cloud Console](https://console.cloud.google.com/)
- [Documentación de permisos de Firebase](https://firebase.google.com/docs/cloud-messaging/auth-server)
