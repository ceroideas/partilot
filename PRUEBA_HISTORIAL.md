# Guía de Prueba del Sistema de Historial

## 🧪 Cómo Probar el Sistema

### 1. Verificar que la migración se ejecutó correctamente

```bash
# Verificar que la tabla existe
php artisan tinker
```

En Tinker:
```php
// Verificar que la tabla existe
Schema::hasTable('participation_activity_logs');
// Debería retornar: true

// Verificar que el modelo funciona
App\Models\ParticipationActivityLog::count();
// Debería retornar: 0 (si no hay registros aún)
```

### 2. Probar el registro automático

En Tinker:
```php
// Obtener una participación existente
$participation = App\Models\Participation::first();

// Si no hay participaciones, el Observer se activará cuando se creen nuevas

// Ver el historial actual
$participation->activityLogs()->count();

// Actualizar la participación para probar el Observer
$participation->update(['status' => 'vendida']);

// Verificar que se creó el log
$participation->activityLogs()->count();
// Debería haber aumentado en 1

// Ver el último log
$log = $participation->activityLogs()->first();
$log->activity_type;  // 'sold'
$log->description;    // 'Participación vendida'
```

### 3. Probar la API

#### Obtener historial de una participación

```bash
# Método GET (usar en navegador o Postman)
http://localhost/participations/1/history
```

Debería retornar JSON con:
```json
{
  "success": true,
  "participation": {
    "code": "...",
    "number": 1,
    "status": "..."
  },
  "activities": [...]
}
```

#### Obtener actividades recientes

```bash
http://localhost/activity-logs/recent?days=7&limit=20
```

#### Obtener estadísticas

```bash
http://localhost/activity-logs/stats
```

### 4. Probar diferentes escenarios

#### Escenario 1: Crear una nueva participación
```php
// Esto registrará automáticamente una actividad de tipo 'created'
$participation = App\Models\Participation::create([
    'entity_id' => 1,
    'set_id' => 1,
    'design_format_id' => 1,
    'participation_number' => 9999,
    'participation_code' => 'TEST/9999',
    'book_number' => 1,
    'status' => 'disponible'
]);

// Ver el log creado
$participation->activityLogs()->first();
```

#### Escenario 2: Asignar a un vendedor
```php
$participation = App\Models\Participation::find(1);
$participation->update([
    'status' => 'asignada',
    'seller_id' => 1
]);

// Debería crear un log de tipo 'assigned'
$participation->activityLogs()->where('activity_type', 'assigned')->first();
```

#### Escenario 3: Vender la participación
```php
$participation->update([
    'status' => 'vendida',
    'sale_amount' => 100,
    'buyer_name' => 'Juan Pérez'
]);

// Debería crear un log de tipo 'sold'
$participation->activityLogs()->where('activity_type', 'sold')->first();
```

#### Escenario 4: Devolución por vendedor
```php
$participation->update([
    'status' => 'disponible',
    'seller_id' => null
]);

// Debería crear un log de tipo 'returned_by_seller'
$participation->activityLogs()->where('activity_type', 'returned_by_seller')->first();
```

#### Escenario 5: Anular participación
```php
$participation->update([
    'status' => 'anulada',
    'cancellation_reason' => 'Motivo de prueba'
]);

// Debería crear un log de tipo 'cancelled'
$participation->activityLogs()->where('activity_type', 'cancelled')->first();
```

### 5. Consultas útiles para verificar

```php
// Ver todos los logs de una participación
App\Models\Participation::find(1)->activityLogs;

// Ver logs con relaciones cargadas
App\Models\Participation::with('activityLogs.user', 'activityLogs.seller')
    ->find(1)
    ->activityLogs;

// Ver solo logs de asignación
App\Models\ParticipationActivityLog::assigned()->get();

// Ver logs de los últimos 7 días
App\Models\ParticipationActivityLog::recent(7)->get();

// Ver logs de un vendedor específico
App\Models\ParticipationActivityLog::bySeller(1)->get();

// Ver estadísticas por tipo
App\Models\ParticipationActivityLog::selectRaw('activity_type, count(*) as total')
    ->groupBy('activity_type')
    ->get();
```

### 6. Verificar que se evitan duplicados

```php
// Este cambio debería crear SOLO UN LOG (no dos)
$participation = App\Models\Participation::find(1);
$countBefore = $participation->activityLogs()->count();

$participation->update([
    'status' => 'asignada',
    'seller_id' => 2,
    'sale_date' => now()
]);

$countAfter = $participation->activityLogs()->count();
echo "Se crearon " . ($countAfter - $countBefore) . " logs";
// Debería decir: "Se crearon 1 logs"
```

### 7. Probar con el flujo real de la aplicación

1. Ve a la aplicación web
2. Navega a una participación
3. Realiza cambios (asignar vendedor, marcar como vendida, etc.)
4. Verifica en la base de datos:

```sql
SELECT 
    pal.*,
    p.participation_code,
    u.name as user_name,
    s.name as seller_name
FROM participation_activity_logs pal
LEFT JOIN participations p ON pal.participation_id = p.id
LEFT JOIN users u ON pal.user_id = u.id
LEFT JOIN sellers s ON pal.seller_id = s.id
ORDER BY pal.created_at DESC
LIMIT 20;
```

## ✅ Checklist de Verificación

- [ ] La tabla `participation_activity_logs` existe
- [ ] El modelo `ParticipationActivityLog` funciona
- [ ] El Observer se ejecuta al crear participaciones
- [ ] El Observer se ejecuta al actualizar participaciones
- [ ] Se registran correctamente las asignaciones a vendedores
- [ ] Se registran correctamente las ventas
- [ ] Se registran correctamente las devoluciones
- [ ] Se registran correctamente las anulaciones
- [ ] No se crean logs duplicados
- [ ] Las APIs retornan datos correctos
- [ ] Los logs incluyen metadata relevante
- [ ] Se registra el usuario que realizó la acción
- [ ] Se registra la IP y User Agent

## 🐛 Problemas Comunes

### "Class ParticipationActivityLog not found"
**Solución:** Ejecutar `composer dump-autoload`

### "Base table or view not found: participation_activity_logs"
**Solución:** Ejecutar la migración específica:
```bash
php artisan migrate --path=database/migrations/2025_10_14_102902_create_participation_activity_logs_table.php
```

### Los logs no se crean automáticamente
**Solución:** Verificar que el Observer esté registrado en `AppServiceProvider::boot()`

### Se crean logs duplicados
**Solución:** Ya está optimizado en el Observer con `return` statements. Si ocurre, revisar la lógica de casos en el método `updated()`.

