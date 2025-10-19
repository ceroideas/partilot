# 🔴 Solución al Error 404 de Firebase

## El Problema

Firebase está devolviendo **404 Not Found** al intentar enviar notificaciones. Esto ocurre porque:

1. ✗ El Server Key no corresponde al proyecto correcto
2. ✗ La Cloud Messaging API (Legacy) está deshabilitada
3. ✗ El proyecto de Firebase fue eliminado o no es accesible

## Solución 1: Verificar y Corregir el Server Key (RÁPIDO)

### Paso 1: Obtener el Server Key Correcto

1. Ve a: https://console.firebase.google.com/
2. **IMPORTANTE**: Selecciona el proyecto **"inicio-de-sesion-94ddc"**
3. Click en ⚙️ (Settings) → **Project Settings**
4. Pestaña: **Cloud Messaging**
5. Desplázate hacia abajo hasta **"Cloud Messaging API (Legacy)"**

### Paso 2: Tres Escenarios Posibles

#### Escenario A: Ves el Server Key
```
✓ Cloud Messaging API (Legacy)
  Server key: AAAAxxxxxxxxx...
```

**Acción:**
1. Copia el Server Key COMPLETO
2. Abre tu archivo `.env`
3. Actualiza o agrega:
   ```env
   FIREBASE_SERVER_KEY=AAAAxxxxxxxxx_tu_key_completa_aqui
   ```
4. Guarda el archivo
5. Ejecuta:
   ```bash
   php artisan config:clear
   php verify-firebase-config.php
   ```

#### Escenario B: Dice "Cloud Messaging API (Legacy) is disabled"
```
⚠️ Cloud Messaging API (Legacy) is disabled
   [Enable] button
```

**Acción:**
1. Haz clic en el botón **"Enable"**
2. Espera 2-5 minutos para que se active
3. Refresca la página
4. Copia el Server Key que aparecerá
5. Sigue los pasos del Escenario A

#### Escenario C: NO aparece "Cloud Messaging API (Legacy)"
```
✓ Cloud Messaging API (V1)
  [Manage API in Google Cloud Console]
```

**Acción:**
Necesitas usar la API V1 (ver Solución 2 abajo)

### Paso 3: Verificar que el Server Key es del Proyecto Correcto

El Server Key debe ser del proyecto **"inicio-de-sesion-94ddc"** que tiene:
- Project ID: `inicio-de-sesion-94ddc`
- Sender ID: `204683025370`
- App ID: `1:204683025370:web:c424b261eff8d566be7ee3`

**Verificación:**
En Firebase Console, verifica que estés viendo el proyecto correcto mirando el nombre en la parte superior de la página.

## Solución 2: Usar Firebase Cloud Messaging API V1 (RECOMENDADO)

Si la API Legacy no está disponible, es mejor usar la API V1 que es más moderna y segura.

### Requisitos:
1. Archivo JSON de credenciales de Service Account
2. Actualizar el código para usar la nueva API

### Pasos:

#### 1. Obtener el Service Account JSON

1. Ve a Firebase Console → Project Settings
2. Pestaña: **Service Accounts**
3. Haz clic en **"Generate new private key"**
4. Se descargará un archivo JSON
5. Guarda el archivo como `storage/firebase-credentials.json`
6. **IMPORTANTE**: Añade este archivo a `.gitignore`

#### 2. Instalar Dependencia

```bash
composer require kreait/firebase-php
```

#### 3. Actualizar el .env

```env
FIREBASE_CREDENTIALS_PATH=storage/firebase-credentials.json
```

#### 4. Actualizar FirebaseService.php

Reemplaza el método `sendRequest` con la nueva implementación V1.

**¿Quieres que implemente esta solución?** Es la más moderna y recomendada por Firebase.

## Solución 3: Verificar el Proyecto de Firebase

### Verifica que el proyecto existe:

1. Ve a https://console.firebase.google.com/
2. Verifica que **"inicio-de-sesion-94ddc"** aparece en tu lista de proyectos
3. Haz clic en el proyecto
4. Verifica que puedes acceder a **Cloud Messaging**

### Si el proyecto no aparece:

Es posible que:
- El proyecto fue eliminado
- No tienes permisos de acceso
- Estás usando una cuenta de Google diferente

**Solución:**
- Usa la cuenta de Google correcta
- Pide acceso al administrador del proyecto
- O crea un nuevo proyecto de Firebase (y actualiza toda la configuración)

## Testing Rápido

Después de aplicar cualquier solución:

```bash
# Limpia configuración
php artisan config:clear

# Verifica la configuración
php verify-firebase-config.php

# Si todo está OK, intenta enviar una notificación
php test-firebase-notification.php
```

## Logs para Diagnosticar

Si aún tienes problemas, revisa los logs:

```bash
# Ver últimos logs
Get-Content storage\logs\laravel.log -Tail 100
```

Busca especialmente:
```
❌ Error en la respuesta de Firebase
```

Y el código de estado:
- **404**: Server Key incorrecto o API deshabilitada
- **401**: Server Key inválido o expirado
- **400**: Formato de request incorrecto
- **403**: Permisos insuficientes

## ¿Cuál Solución Elegir?

### Usa Solución 1 (Legacy API) si:
- ✓ Quieres la solución más rápida
- ✓ Solo necesitas cambiar el Server Key
- ✓ La API Legacy está disponible

### Usa Solución 2 (API V1) si:
- ✓ La API Legacy no está disponible
- ✓ Quieres una solución a largo plazo
- ✓ Quieres mejor seguridad
- ✓ No te importa instalar una dependencia adicional

## Siguiente Paso

**Dime cuál escenario ves en Firebase Console** (A, B, o C) y procederemos con la solución correspondiente.

