# 📋 Instrucciones para Probar el Sistema de Historial

## ✅ Sistema Completo Implementado

Todo está configurado y listo para usar. Aquí están las instrucciones para probarlo:

## 🧪 Pruebas a Realizar

### 1️⃣ Asignar Participación a un Vendedor

**Pasos:**
1. Ve a la vista de un vendedor
2. Asigna una o varias participaciones al vendedor
3. Verifica que se muestre el mensaje de éxito

**Qué debería pasar:**
- Se creará un log de tipo `assigned` (Asignada a vendedor)
- El log registrará el cambio de estado y el vendedor asignado
- Se guardará la fecha/hora, usuario e IP

**Verificar en base de datos:**
```sql
SELECT * FROM participation_activity_logs 
WHERE activity_type = 'assigned' 
ORDER BY created_at DESC 
LIMIT 5;
```

---

### 2️⃣ Devolver Participación del Vendedor

**Pasos:**
1. Ve a la vista del vendedor que tiene participaciones asignadas
2. Elimina una asignación (devuelve una participación)
3. Verifica que la participación regrese a estado disponible

**Qué debería pasar:**
- Se creará un log de tipo `returned_by_seller` (Devuelta por vendedor)
- El log registrará el vendedor que devolvió la participación
- Se guardará toda la información del cambio

**Verificar en base de datos:**
```sql
SELECT * FROM participation_activity_logs 
WHERE activity_type = 'returned_by_seller' 
ORDER BY created_at DESC 
LIMIT 5;
```

---

### 3️⃣ Crear una Devolución

**Pasos:**
1. Ve al módulo de devoluciones
2. Crea una nueva devolución
3. Selecciona participaciones para devolver
4. Selecciona participaciones para vender (liquidación)
5. Procesa la devolución

**Qué debería pasar:**
- Para cada participación devuelta: Se creará un log de tipo `returned_to_administration`
- Para cada participación vendida en liquidación: Se creará un log de tipo `sold`
- Se registrarán todos los cambios de estado

**Verificar en base de datos:**
```sql
SELECT * FROM participation_activity_logs 
WHERE activity_type IN ('returned_to_administration', 'sold')
AND DATE(created_at) = CURDATE()
ORDER BY created_at DESC;
```

---

### 4️⃣ Ver Historial en la Vista de Participación

**Pasos:**
1. Navega a cualquier participación: `/participations/view/{id}`
2. Desplázate hasta la sección "Historial Participación"
3. Espera a que cargue la tabla

**Qué debería ver:**
- Tabla con todas las actividades de esa participación
- Cada actividad con:
  - Fecha y hora
  - Tipo de actividad (badge con color)
  - Descripción
  - Usuario que realizó la acción
  - Vendedor (si aplica)
- Botón "Actualizar" para refrescar

**Si hace clic en una fila:**
- Debería abrirse un modal con detalles completos
- Mostrará metadata adicional
- Mostrará la IP del usuario

---

### 5️⃣ Verificar Logs Manualmente

**Opción A - SQL:**
```sql
-- Ver todos los logs
SELECT 
    pal.id,
    pal.activity_type,
    pal.description,
    p.participation_code,
    u.name as usuario,
    s.name as vendedor,
    pal.created_at
FROM participation_activity_logs pal
LEFT JOIN participations p ON pal.participation_id = p.id
LEFT JOIN users u ON pal.user_id = u.id
LEFT JOIN sellers s ON pal.seller_id = s.id
ORDER BY pal.created_at DESC
LIMIT 20;

-- Contar logs por tipo
SELECT activity_type, COUNT(*) as total 
FROM participation_activity_logs 
GROUP BY activity_type;

-- Logs de hoy
SELECT * FROM participation_activity_logs 
WHERE DATE(created_at) = CURDATE() 
ORDER BY created_at DESC;
```

**Opción B - Tinker:**
```php
php artisan tinker

// Ver total de logs
App\Models\ParticipationActivityLog::count()

// Ver últimos 10 logs
App\Models\ParticipationActivityLog::with(['user', 'seller'])
    ->latest()
    ->take(10)
    ->get()

// Ver logs de una participación específica
$participation = App\Models\Participation::find(1);
$participation->activityLogs

// Ver logs por tipo
App\Models\ParticipationActivityLog::assigned()->count()
App\Models\ParticipationActivityLog::sold()->count()
App\Models\ParticipationActivityLog::returnedBySeller()->count()
```

---

### 6️⃣ Probar la API Directamente

**Obtener historial de una participación:**
```
GET /participations/{id}/history
```
Ejemplo: `http://localhost/participations/1/history`

**Respuesta esperada:**
```json
{
  "success": true,
  "participation": {
    "code": "1/00001",
    "number": 1,
    "status": "asignada"
  },
  "activities": [
    {
      "id": 2,
      "activity_type": "assigned",
      "activity_type_text": "Asignada a vendedor",
      "activity_badge": "bg-primary",
      "description": "Participación asignada al vendedor ID: 1",
      "user": "Admin",
      "seller": "Juan Pérez",
      "created_at": "14/10/2025 11:30:00"
    },
    {
      "id": 1,
      "activity_type": "created",
      "activity_type_text": "Creada",
      "activity_badge": "bg-info",
      "description": "Participación #1 creada",
      "user": "Sistema",
      "created_at": "14/10/2025 08:00:00"
    }
  ]
}
```

**Obtener actividades recientes:**
```
GET /activity-logs/recent?days=7&limit=20
```

**Obtener estadísticas:**
```
GET /activity-logs/stats
```

---

## 📊 Tipos de Actividades Registradas

| Tipo | Cuándo se registra | Badge |
|------|-------------------|-------|
| `created` | Al crear la participación | 🔵 bg-info |
| `assigned` | Al asignar a un vendedor | 🟣 bg-primary |
| `returned_by_seller` | Al devolver el vendedor | ⚠️ bg-warning |
| `sold` | Al vender | ✅ bg-success |
| `returned_to_administration` | Al devolver a admin | ⚪ bg-secondary |
| `status_changed` | Cambio de estado genérico | 🔵 bg-info |
| `cancelled` | Al anular | 🔴 bg-danger |
| `modified` | Al modificar datos | ⚫ bg-secondary |

---

## ⚠️ Solución de Problemas

### No se crean logs

**Posible causa:** Estás usando `DB::table()` en lugar del modelo Eloquent

**Solución:** Usar siempre el modelo:
```php
// ✅ CORRECTO
$participation = Participation::find($id);
$participation->update(['status' => 'vendida']);

// ❌ INCORRECTO (no dispara Observer)
DB::table('participations')->where('id', $id)->update(['status' => 'vendida']);
```

### La vista no carga el historial

**Verificar:**
1. Que estás en la ruta correcta: `/participations/view/{id}`
2. Abrir consola del navegador (F12) y buscar errores
3. Verificar que la API responda: `/participations/{id}/history`

**Solución común:**
```bash
php artisan config:clear
php artisan cache:clear
```

### Error 500 en la API

**Verificar:**
```bash
# Ver logs de Laravel
tail -f storage/logs/laravel.log
```

---

## ✅ Checklist Final

- [ ] Asignar participación a vendedor → Ver log creado
- [ ] Devolver participación → Ver log de devolución
- [ ] Abrir vista de participación → Ver historial cargado
- [ ] Hacer clic en una fila → Ver modal con detalles
- [ ] Verificar que se guarda IP y usuario
- [ ] Probar botón "Actualizar"
- [ ] Verificar API funciona correctamente

---

## 🎉 ¡Listo!

Si todas las pruebas pasan, el sistema está funcionando correctamente y listo para producción.

**Archivos clave:**
- **Modelo:** `app/Models/ParticipationActivityLog.php`
- **Observer:** `app/Observers/ParticipationObserver.php`
- **Controlador:** `app/Http/Controllers/ParticipationActivityLogController.php`
- **Vista:** `resources/views/participations/show.blade.php`
- **Rutas:** `routes/web.php`
- **Migración:** `database/migrations/2025_10_14_102902_create_participation_activity_logs_table.php`

**Documentación:**
- `HISTORIAL_PARTICIPACIONES.md` - Documentación completa del sistema
- `SOLUCION_OBSERVER.md` - Solución al problema encontrado
- `PRUEBA_HISTORIAL.md` - Guía de pruebas
- `INSTRUCCIONES_PRUEBA.md` - Este archivo

