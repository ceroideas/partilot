# 🔴 Solución para Error `invalid_grant`

## El Problema

Error en el servidor pero funciona en local:
```
❌ Error al enviar notificación: invalid_grant
```

## ✅ SOLUCIÓN DEFINITIVA

### Paso 1: Habilitar Firebase Cloud Messaging API

**IMPORTANTE:** Esta es la causa más común del error.

1. Abre este enlace (debes estar logueado con la cuenta de Google del proyecto):
   ```
   https://console.cloud.google.com/apis/library/fcm.googleapis.com?project=inicio-de-sesion-94ddc
   ```

2. Haz clic en el botón azul **"ENABLE"** (Habilitar)

3. Espera a que se active (puede tomar 1-2 minutos)

### Paso 2: Verificar permisos de la cuenta de servicio

1. Ve a IAM:
   ```
   https://console.cloud.google.com/iam-admin/iam?project=inicio-de-sesion-94ddc
   ```

2. Busca tu cuenta de servicio:
   ```
   partilot@inicio-de-sesion-94ddc.iam.gserviceaccount.com
   ```

3. Debe tener UNO de estos roles:
   - ✅ **Firebase Admin SDK Administrator Service Agent**
   - ✅ **Firebase Cloud Messaging Admin**
   - ✅ **Editor** (mínimo)

4. Si NO tiene ninguno, haz clic en **EDIT** (lápiz) → **ADD ANOTHER ROLE** → Busca "Firebase Admin" → Guarda

### Paso 3: Limpiar caché en el servidor

```bash
cd /var/www/vhosts/ceroideas.es/httpdocs/partilot
php artisan config:clear
php artisan cache:clear
composer dump-autoload
```

### Paso 4: Probar de nuevo

```bash
php artisan firebase:diagnose
```

## 🔍 Verificación

Después de habilitar la API, espera 2-3 minutos y prueba enviar una notificación desde:
```
https://ceroideas.es/partilot/public/notifications/dashboard
```

## 💡 Otras causas posibles

Si el error persiste después de habilitar la API:

### Causa 2: Problemas con la librería de Firebase

```bash
cd /var/www/vhosts/ceroideas.es/httpdocs/partilot

# Reinstalar dependencias de composer
rm -rf vendor/
composer install --no-dev --optimize-autoloader

# Limpiar caché
php artisan config:clear
php artisan cache:clear
```

### Causa 3: Conflicto con extensiones PHP

Verificar extensiones instaladas:
```bash
php -m | grep -E "openssl|curl|json|mbstring"
```

Todas deben aparecer. Si falta alguna:
```bash
sudo apt-get install php8.1-openssl php8.1-curl php8.1-mbstring php8.1-json
sudo service apache2 restart
```

### Causa 4: Variables de entorno

Verifica que `.env` en el servidor tenga:
```env
FIREBASE_PROJECT_ID=inicio-de-sesion-94ddc
```

Luego:
```bash
php artisan config:clear
```

## 📊 Log esperado después de la solución

```
✅ Firebase Service Modern inicializado correctamente
📤 Enviando notificación a dispositivo individual
✅ Notificación enviada exitosamente
📊 Resultado: 1 exitosos, 0 fallidos
```

## 🆘 Si aún no funciona

Ejecuta este comando en el servidor y envíame el output completo:

```bash
cd /var/www/vhosts/ceroideas.es/httpdocs/partilot
php artisan firebase:diagnose --send-test 2>&1 | tee firebase-debug.log
cat storage/logs/laravel.log | tail -50
```
