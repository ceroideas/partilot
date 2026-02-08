# Guía de Implementación de API para App Ionic

## 📋 Resumen

Se ha creado una estructura completa de API REST en `routes/api.php` que conecta la aplicación Ionic con el sistema Laravel. La API está organizada por módulos funcionales y utiliza Laravel Sanctum para autenticación.

## ✅ Lo que ya está implementado

### 1. **Autenticación** ✅
- ✅ `POST /api/auth/login` - Login de usuarios
- ✅ `POST /api/auth/register` - Registro de usuarios (si aplica)
- ✅ `GET /api/auth/user` - Obtener usuario autenticado
- ✅ `POST /api/auth/logout` - Cerrar sesión
- ✅ `POST /api/auth/refresh` - Refrescar token
- ✅ `GET /api/auth/verify` - Verificar token

**Controlador:** `AuthController` - Métodos `apiLogin`, `apiRegister`, `apiLogout`, `apiRefresh` implementados.

### 2. **Estructura de Rutas Creada**

La API está organizada en los siguientes módulos:

#### 🔐 Autenticación (`/api/auth/*`)
- Login, registro, logout, refresh token

#### 👤 Perfil (`/api/profile/*`)
- Obtener perfil, actualizar perfil, cambiar contraseña, subir avatar

#### 🎫 Participaciones (`/api/participations/*`)
- Listar, obtener, crear, vender, digitalizar, regalar, buscar

#### 💰 Ventas (`/api/sales/*`)
- Venta por QR, venta manual, estadísticas

#### 👥 Vendedores (`/api/sellers/*`)
- Listar, obtener, asignar participaciones, liquidaciones

#### 🔔 Notificaciones (`/api/notifications/*`)
- Listar, obtener, marcar como leída, contar no leídas

#### 🎲 Loterías (`/api/lotteries/*`)
- Listar, obtener, resultados, tipos

#### 📊 Resultados (`/api/results/*`)
- Verificar ganadores, obtener resultados

#### 💼 Cartera (`/api/wallet/*`)
- Obtener cartera, movimientos, historial

#### 💳 Pagos (`/api/payments/*`)
- Cobros disponibles, solicitar cobro, historial

#### 🛠️ Gestión (`/api/management/*`)
- Participaciones, vendedores, devoluciones, pagos (solo gestores)

#### 🏢 Entidades (`/api/entities/*`)
- Listar, obtener, loterías, vendedores

#### 📦 Reservas y Sets (`/api/reserves/*`, `/api/sets/*`)
- Listar, obtener, participaciones

#### 🎨 Utilidades (`/api/utils/*`)
- Subir imágenes, generar QR, verificar eliminación

## 🚧 Lo que falta implementar

### Métodos API pendientes en controladores

Los siguientes métodos están referenciados en las rutas pero aún no están implementados en los controladores. Se pueden ir creando según se necesiten:

#### ParticipationController
- `apiIndex()` - Listar participaciones
- `apiShow($id)` - Obtener participación
- `apiStore()` - Crear/asignar participación
- `apiSell($id)` - Vender participación
- `apiDigitalize()` - Digitalizar (escanear QR)
- `apiGift($id)` - Regalar participación
- `apiGetBySeller($sellerId)` - Por vendedor
- `apiGetHistory($id)` - Historial
- `apiSearch($code)` - Buscar por código
- `apiSellByQr()` - Venta por QR
- `apiSellManual()` - Venta manual
- `apiGetSalesBySeller($sellerId)` - Ventas por vendedor
- `apiGetSalesStats()` - Estadísticas de ventas
- `apiGetWalletParticipations()` - Participaciones en cartera
- `apiManagementIndex()` - Gestión de participaciones
- `apiGetManagementStats()` - Estadísticas de gestión
- `apiBulkAssign()` - Asignación masiva

#### SellerController
- `apiIndex()` - Listar vendedores
- `apiShow($id)` - Obtener vendedor
- `apiAssignParticipations($id)` - Asignar participaciones
- `apiGetParticipations($id)` - Obtener participaciones
- `apiGetStats($id)` - Estadísticas
- `apiStore()` - Crear vendedor
- `apiUpdate($id)` - Actualizar vendedor
- `apiDestroy($id)` - Eliminar vendedor
- `apiManagementIndex()` - Gestión de vendedores

#### NotificationController
- `apiIndex()` - Listar notificaciones
- `apiShow($id)` - Obtener notificación
- `apiMarkAsRead($id)` - Marcar como leída
- `apiMarkAllAsRead()` - Marcar todas como leídas
- `apiUnreadCount()` - Contar no leídas
- `apiDestroy($id)` - Eliminar notificación

#### LotteryController
- `apiIndex()` - Listar loterías
- `apiShow($id)` - Obtener lotería
- `apiGetResults($id)` - Obtener resultados
- `apiGetResultsByAdministration($id, $administrationId)` - Resultados por administración
- `apiGetAvailable()` - Loterías disponibles
- `apiGetTypes()` - Tipos de lotería

#### UserController
- `apiGetProfile()` - Obtener perfil
- `apiUpdateProfile()` - Actualizar perfil
- `apiChangePassword()` - Cambiar contraseña
- `apiUploadAvatar()` - Subir avatar
- `apiGetWallet()` - Obtener cartera
- `apiGetMovements()` - Obtener movimientos
- `apiGetHistory()` - Obtener historial
- `apiGetAvailablePayments()` - Cobros disponibles
- `apiRequestPayment()` - Solicitar cobro
- `apiGetPaymentHistory()` - Historial de cobros
- `apiGetPaymentDetails($id)` - Detalles de cobro

#### DevolutionsController
- `apiIndex()` - Listar devoluciones
- `apiStore()` - Crear devolución
- `apiShow($id)` - Obtener devolución
- `apiUpdate($id)` - Actualizar devolución
- `apiDestroy($id)` - Eliminar devolución

#### ManagerController
- `apiGetPayments()` - Obtener pagos
- `apiCreatePayment()` - Crear pago
- `apiGetPaymentDetails($id)` - Detalles de pago

#### EntityController
- `apiIndex()` - Listar entidades
- `apiShow($id)` - Obtener entidad
- `apiGetLotteries($id)` - Loterías de entidad
- `apiGetSellers($id)` - Vendedores de entidad

#### ReserveController
- `apiIndex()` - Listar reservas
- `apiShow($id)` - Obtener reserva
- `apiGetSets($id)` - Sets de reserva

#### SetController
- `apiIndex()` - Listar sets
- `apiShow($id)` - Obtener set
- `apiGetParticipations($id)` - Participaciones del set

#### ApiController
- `apiCheckWinning()` - Verificar si ganó
- `apiGetParticipationResults($participationId)` - Resultados de participación

## 🔌 Qué se puede conectar primero

### Prioridad Alta (Funcionalidades básicas)

1. **Autenticación** ✅ **YA IMPLEMENTADO**
   - La app puede hacer login y obtener token
   - Endpoint: `POST /api/auth/login`

2. **Perfil de Usuario**
   - Obtener datos del usuario autenticado
   - Endpoint: `GET /api/auth/user` (ya funciona)
   - Falta: `GET /api/profile/`, `PUT /api/profile/`

3. **Notificaciones**
   - Listar notificaciones del usuario
   - Endpoints: `/api/notifications/*`
   - **Acción:** Implementar métodos en `NotificationController`

4. **Participaciones - Ver/Listar**
   - Ver participaciones del usuario/vendedor
   - Endpoints: `GET /api/participations/`, `GET /api/participations/{id}`
   - **Acción:** Implementar `apiIndex()` y `apiShow()` en `ParticipationController`

### Prioridad Media (Funcionalidades principales)

5. **Venta de Participaciones**
   - Vender participación por QR o manual
   - Endpoints: `POST /api/sales/qr`, `POST /api/sales/manual`
   - **Acción:** Implementar métodos de venta

6. **Digitalizar Participación**
   - Escanear QR para digitalizar
   - Endpoint: `POST /api/participations/digitalize`
   - **Acción:** Implementar `apiDigitalize()`

7. **Resultados y Loterías**
   - Ver resultados de sorteos
   - Endpoints: `/api/lotteries/*`, `/api/results/*`
   - **Acción:** Implementar métodos en `LotteryController`

8. **Cartera y Movimientos**
   - Ver cartera del usuario
   - Endpoints: `/api/wallet/*`
   - **Acción:** Implementar métodos en `UserController`

### Prioridad Baja (Funcionalidades avanzadas)

9. **Gestión (Solo para gestores)**
   - Gestionar vendedores, devoluciones, pagos
   - Endpoints: `/api/management/*`
   - **Acción:** Implementar métodos de gestión

10. **Regalar Participación**
    - Regalar participación a otro usuario
    - Endpoint: `POST /api/participations/{id}/gift`
    - **Acción:** Implementar `apiGift()`

## 📝 Ejemplo de uso

### Login desde Ionic

```typescript
// En tu servicio de Ionic
login(email: string, password: string) {
  return this.http.post<any>('https://tu-dominio.com/api/auth/login', {
    email,
    password
  }).pipe(
    tap(response => {
      if (response.success) {
        // Guardar token
        localStorage.setItem('token', response.token);
        // Guardar usuario
        localStorage.setItem('user', JSON.stringify(response.user));
      }
    })
  );
}
```

### Obtener participaciones

```typescript
getParticipations() {
  const token = localStorage.getItem('token');
  return this.http.get('https://tu-dominio.com/api/participations', {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
}
```

## 🔒 Autenticación

La API utiliza **Laravel Sanctum** para autenticación mediante tokens. 

- El token se obtiene al hacer login
- Se envía en el header: `Authorization: Bearer {token}`
- El token se puede refrescar con `POST /api/auth/refresh`

## 📌 Notas importantes

1. **Middleware de autenticación:** Todas las rutas excepto login/register requieren `auth:sanctum`
2. **Middleware de roles:** Algunas rutas de gestión requieren roles específicos (`role:super_admin,administration,entity`)
3. **Validación:** Cada método debe validar los datos de entrada
4. **Respuestas JSON:** Todas las respuestas deben ser en formato JSON
5. **Códigos HTTP:** Usar códigos HTTP apropiados (200, 201, 400, 401, 403, 404, 422, 500)

## 🚀 Próximos pasos

1. Implementar los métodos API más prioritarios (autenticación ya está ✅)
2. Probar los endpoints con Postman o similar
3. Conectar desde la app Ionic empezando por login y perfil
4. Ir implementando métodos según se vayan necesitando

## 📚 Recursos

- Documentación Laravel Sanctum: https://laravel.com/docs/sanctum
- Rutas definidas en: `routes/api.php`
- Controladores en: `app/Http/Controllers/`
