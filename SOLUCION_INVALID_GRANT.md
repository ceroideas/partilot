# 🔧 Solución: Error "invalid_grant" en Firebase

## ❌ Problema

El error `invalid_grant` indica que Firebase no puede validar las credenciales de la cuenta de servicio.

```
Error: invalid_grant
```

## ✅ Soluciones

### **Solución 1: Habilitar Firebase Cloud Messaging API**

Este es el paso MÁS IMPORTANTE:

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Selecciona el proyecto: `inicio-de-sesion-94ddc`
3. Ve a **APIs & Services** → **Library**
4. Busca: **Firebase Cloud Messaging API**
5. Haz clic en **ENABLE** (Habilitar)

### **Solución 2: Verificar Permisos de la Cuenta de Servicio**

1. Ve a [Google Cloud Console IAM](https://console.cloud.google.com/iam-admin/iam)
2. Busca: `partilot@inicio-de-sesion-94ddc.iam.gserviceaccount.com`
3. Haz clic en **Editar** (ícono de lápiz)
4. Agrega estos roles:
   - ✅ **Firebase Admin** (`roles/firebase.admin`)
   - ✅ **Cloud Messaging Admin** (`roles/cloudmessaging.admin`)
   - ✅ **Firebase Cloud Messaging Admin** (`roles/firebasemessaging.admin`)

### **Solución 3: Sincronizar Reloj del Servidor**

Si estás en un servidor Linux/Windows, sincroniza el reloj:

**Windows:**
```cmd
w32tm /resync
```

**Linux:**
```bash
sudo ntpdate -s time.nist.gov
```

### **Solución 4: Regenerar Credenciales**

Si nada funciona, regenera las credenciales:

1. Firebase Console → **Project Settings** → **Service Accounts**
2. Haz clic en **Generate new private key**
3. Descarga el nuevo archivo JSON
4. Reemplaza `storage/firebase-credentials.json`

## 🧪 Probar después de aplicar las soluciones

```bash
php test-firebase-debug.php
```

Deberías ver:
```
✅ ¡ÉXITO!
✓ Message ID: projects/...
```

## 📝 Orden de acciones recomendado

1. **PRIMERO**: Habilitar Firebase Cloud Messaging API (Solución 1)
2. **SEGUNDO**: Verificar permisos (Solución 2)
3. **TERCERO**: Si persiste, sincronizar reloj (Solución 3)
4. **ÚLTIMO RECURSO**: Regenerar credenciales (Solución 4)

## 🔗 Enlaces directos

- [Google Cloud Console](https://console.cloud.google.com/)
- [Firebase Console](https://console.firebase.google.com/)
- [IAM & Admin](https://console.cloud.google.com/iam-admin/iam?project=inicio-de-sesion-94ddc)
- [APIs & Services](https://console.cloud.google.com/apis/library?project=inicio-de-sesion-94ddc)

---

**¿Por qué ocurre este error?**

El error `invalid_grant` ocurre cuando Firebase no puede verificar que la cuenta de servicio tiene autorización para enviar mensajes. Esto es típicamente porque:

- La API de FCM no está habilitada
- La cuenta de servicio no tiene los roles correctos
- El token JWT generado no es válido (problema de reloj)

La solución más común es **habilitar la Firebase Cloud Messaging API**.
