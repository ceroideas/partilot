# Sistema de Historial de Actividades para Participaciones

## 📋 Descripción

Se ha implementado un sistema completo de auditoría y registro de actividades para las participaciones. Este sistema registra automáticamente todas las acciones que se realizan sobre cada participación.

## 🗄️ Base de Datos

### Tabla: `participation_activity_logs`

Ubicación: `database/migrations/2025_10_14_102902_create_participation_activity_logs_table.php`

**Campos principales:**
- `participation_id` - Referencia a la participación
- `activity_type` - Tipo de actividad (enum)
- `user_id` - Usuario que realizó la acción
- `seller_id` - Vendedor involucrado
- `entity_id` - Entidad involucrada
- `old_status` / `new_status` - Estados anterior y nuevo
- `old_seller_id` / `new_seller_id` - Vendedores anterior y nuevo
- `description` - Descripción de la actividad
- `metadata` - Datos adicionales en JSON
- `ip_address` - Dirección IP del usuario
- `user_agent` - Navegador/dispositivo usado
- `created_at` / `updated_at` - Timestamps

## 🎯 Tipos de Actividades Registradas

1. **`created`** - Cuando se crea la participación
2. **`assigned`** - Cuando se asigna a un vendedor
3. **`returned_by_seller`** - Cuando el vendedor devuelve la participación (elimina la asignación)
4. **`sold`** - Cuando se vende
5. **`returned_to_administration`** - Cuando la entidad devuelve a la administración
6. **`status_changed`** - Cuando cambia de estado (genérico)
7. **`cancelled`** - Cuando se anula
8. **`modified`** - Cuando se modifica información (comprador, importe, etc.)

## 🔧 Componentes Implementados

### 1. Modelo: `ParticipationActivityLog`
**Ubicación:** `app/Models/ParticipationActivityLog.php`

**Características:**
- Relaciones con Participation, User, Seller, Entity
- Scopes para filtrar por tipo de actividad
- Método estático `log()` para registrar actividades fácilmente
- Atributos calculados para badges y textos

**Uso:**
```php
// Registrar una actividad manualmente
ParticipationActivityLog::log($participationId, 'sold', [
    'seller_id' => $sellerId,
    'description' => 'Participación vendida',
    'metadata' => ['sale_amount' => 100]
]);

// Obtener historial de una participación
$logs = ParticipationActivityLog::forParticipation($id)->get();

// Obtener actividades de un vendedor
$logs = ParticipationActivityLog::bySeller($sellerId)->get();
```

### 2. Observer: `ParticipationObserver`
**Ubicación:** `app/Observers/ParticipationObserver.php`

**Funcionalidad:**
Registra automáticamente todas las actividades cuando:
- Se crea una participación (`created`)
- Se actualiza una participación (`updated`)
  - Detecta y registra diferentes tipos de cambios
  - Evita duplicados con lógica de prioridad
- Se elimina una participación (`deleted`)
- Se restaura una participación (`restored`)

**Casos detectados automáticamente:**

1. **Asignación a vendedor:** Cuando `seller_id` pasa de `null` a un valor y `status` cambia a `'asignada'`
2. **Venta:** Cuando `status` cambia a `'vendida'`
3. **Devolución por vendedor:** Cuando `seller_id` pasa de un valor a `null`
4. **Devolución a administración:** Cuando `status` cambia a `'devuelta'` sin eliminar vendedor
5. **Anulación:** Cuando `status` cambia a `'anulada'`
6. **Reasignación:** Cuando `seller_id` cambia de un vendedor a otro
7. **Modificación de datos:** Cuando cambian datos del comprador o importe

### 3. Controlador: `ParticipationActivityLogController`
**Ubicación:** `app/Http/Controllers/ParticipationActivityLogController.php`

**Métodos disponibles:**
- `getParticipationHistory($id)` - Obtiene historial de una participación
- `getSellerHistory($sellerId)` - Obtiene historial de actividades de un vendedor
- `getEntityHistory($entityId)` - Obtiene historial de actividades de una entidad
- `getActivityStats()` - Obtiene estadísticas de actividad
- `getRecentActivities()` - Obtiene actividades recientes (últimos N días)
- `show($id)` - Vista del historial (por implementar)

### 4. Rutas API
**Ubicación:** `routes/web.php`

```php
// Historial de una participación específica
GET /participations/{id}/history
GET /participations/{id}/activity-log

// Historial por vendedor
GET /activity-logs/seller/{id}
  Query params: activity_type, date_from, date_to

// Historial por entidad
GET /activity-logs/entity/{id}
  Query params: activity_type, date_from, date_to

// Estadísticas
GET /activity-logs/stats
  Query params: seller_id, entity_id, date_from, date_to

// Actividades recientes
GET /activity-logs/recent
  Query params: days (default: 7), limit (default: 50)
```

## 📊 Modelo de Datos

### Relación en Participation Model
```php
// En app/Models/Participation.php
public function activityLogs()
{
    return $this->hasMany(ParticipationActivityLog::class)
                ->orderBy('created_at', 'desc');
}
```

### Uso en consultas
```php
// Obtener participación con su historial
$participation = Participation::with('activityLogs')->find($id);

// Obtener solo ciertos tipos de actividades
$participation->activityLogs()
              ->whereIn('activity_type', ['assigned', 'sold'])
              ->get();
```

## 🔄 Registro Automático

El sistema está configurado para registrar automáticamente en `AppServiceProvider`:

```php
// app/Providers/AppServiceProvider.php
Participation::observe(ParticipationObserver::class);
```

## 📝 Ejemplos de Uso

### Consultar historial de una participación
```php
$history = ParticipationActivityLog::with(['user', 'seller', 'entity'])
    ->forParticipation($participationId)
    ->orderBy('created_at', 'desc')
    ->get();
```

### Obtener actividades de venta de los últimos 30 días
```php
$sales = ParticipationActivityLog::sold()
    ->recent(30)
    ->with('participation')
    ->get();
```

### Estadísticas de un vendedor
```php
$stats = ParticipationActivityLog::bySeller($sellerId)
    ->selectRaw('activity_type, count(*) as count')
    ->groupBy('activity_type')
    ->get();
```

## 🎨 Respuesta JSON de la API

### Ejemplo: GET /participations/{id}/history
```json
{
  "success": true,
  "participation": {
    "code": "001/00001",
    "number": 1,
    "status": "vendida"
  },
  "activities": [
    {
      "id": 1,
      "activity_type": "sold",
      "activity_type_text": "Vendida",
      "activity_badge": "bg-success",
      "description": "Participación vendida",
      "user": "Juan Pérez",
      "seller": "Vendedor A",
      "entity": "Entidad Demo",
      "old_status": "asignada",
      "new_status": "vendida",
      "metadata": {
        "sale_amount": 100,
        "buyer_name": "Cliente X"
      },
      "created_at": "14/10/2025 10:30:00",
      "ip_address": "192.168.1.1"
    }
  ]
}
```

## ✅ Estado Actual

### ✔️ Completado
- [x] Migración de base de datos ejecutada
- [x] Modelo ParticipationActivityLog creado
- [x] Observer ParticipationObserver implementado
- [x] Observer registrado en AppServiceProvider
- [x] Controlador con métodos API creado
- [x] Rutas API configuradas
- [x] Relación en modelo Participation
- [x] Sistema funcionando automáticamente
- [x] Vista del historial integrada en `participations/show.blade.php`
- [x] Interfaz interactiva con carga dinámica vía AJAX/jQuery
- [x] Detalles de actividades al hacer clic en cada fila
- [x] Botón de actualizar para refrescar el historial

### ⏳ Pendiente (según necesidad)
- [ ] Permisos y control de acceso (si es necesario)
- [ ] Exportación de reportes (PDF, Excel, etc.)
- [ ] Widgets adicionales en dashboard (si se requiere)

## 🎨 Vista Implementada

### Ubicación
El historial de actividades se muestra en la vista de detalle de cada participación:
- **Archivo:** `resources/views/participations/show.blade.php`
- **Ruta:** `/participations/view/{id}` (controlador: `ParticipationController@show`)
- **Ubicación en la página:** Sección "Historial Participación" (reemplaza la tabla estática anterior)

### Características de la interfaz

1. **Carga Dinámica:**
   - Al abrir la vista de una participación, se carga automáticamente el historial desde la API
   - Indicador de carga mientras se obtienen los datos
   - Manejo de errores con mensajes amigables

2. **Tabla Interactiva:**
   - Muestra las actividades en una tabla ordenada por fecha (más reciente primero)
   - Columnas: Tipo, Descripción, Usuario, Vendedor, Fecha/Hora
   - Badges de colores según el tipo de actividad
   - Muestra cambios de estado y vendedores directamente en la tabla

3. **Detalles al hacer clic:**
   - Cada fila es clickeable para ver más detalles
   - Modal con información completa (metadata, IP, etc.)
   - Usa SweetAlert2 si está disponible, sino usa alert nativo

4. **Botón de actualizar:**
   - Permite refrescar el historial sin recargar la página
   - Útil para ver cambios recientes

### Estados visuales

- **Loading:** Spinner de carga mientras se obtienen los datos
- **Con actividades:** Tabla con todas las actividades
- **Sin actividades:** Mensaje informativo cuando no hay registros
- **Error:** Mensaje de error si falla la carga

### Tipos de badges (colores)

- 🔵 **bg-info** - Creada
- 🟣 **bg-primary** - Asignada a vendedor
- ⚠️ **bg-warning** - Devuelta por vendedor
- ✅ **bg-success** - Vendida
- ⚪ **bg-secondary** - Devuelta a administración
- 🔴 **bg-danger** - Anulada
- ⚫ **bg-secondary** - Modificada

## 🚀 Uso de la Vista

1. Navega a cualquier participación
2. Desplázate hasta la sección "Historial de Actividades"
3. Verás automáticamente todas las actividades registradas
4. Haz clic en cualquier fila para ver detalles completos
5. Usa el botón de refrescar (🔄) para actualizar el historial

## 💡 Notas Importantes

1. **Registro Automático:** No necesitas hacer nada especial. Cada vez que se crea, actualiza o elimina una participación, se registra automáticamente en el historial.

2. **Evita Duplicados:** El Observer está optimizado para evitar registros duplicados cuando ocurren múltiples cambios simultáneos.

3. **Metadata Flexible:** El campo `metadata` en JSON permite almacenar cualquier información adicional específica de cada tipo de actividad.

4. **Auditoría Completa:** Se registra IP y User Agent para tener un rastro completo de auditoría.

5. **Performance:** Los índices en la base de datos están optimizados para consultas rápidas por participación, vendedor, entidad y tipo de actividad.

