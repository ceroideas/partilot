# Sistema de Notificaciones al Usuario Correcto

## 📋 Resumen

Se ha implementado un sistema completo de notificaciones Firebase que envía las notificaciones **solo a los usuarios correctos** según el contexto de cada evento.

## 🎯 Cambios Implementados

### 1. Observer de Participaciones (ParticipationObserver)

Se ha actualizado el `ParticipationObserver` para que envíe notificaciones automáticas cuando ocurren eventos en las participaciones:

#### Eventos que Generan Notificaciones:

| Evento | Descripción | Usuarios Notificados |
|--------|-------------|---------------------|
| **Asignación** | Se asigna una participación a un vendedor | • Vendedor asignado<br>• Manager de la entidad |
| **Reasignación** | Se cambia de un vendedor a otro | • Vendedor anterior<br>• Nuevo vendedor<br>• Manager de la entidad |
| **Venta** | Se marca como vendida | • Manager de la entidad |
| **Devolución por Vendedor** | El vendedor devuelve la participación | • Vendedor que devuelve<br>• Manager de la entidad |
| **Devolución a Administración** | Se devuelve a la administración | • Manager de la entidad |
| **Anulación** | Se anula la participación | • Manager de la entidad |

#### Métodos Agregados:

- `sendNotification($participation, $event, $data)`: Coordina el envío de notificaciones
- `getRelevantUserTokens($participation, $event)`: Obtiene los usuarios correctos según el evento
- `prepareNotificationContent($participation, $event, $data)`: Prepara el contenido de la notificación

### 2. Controlador de Notificaciones (NotificationController)

Se ha actualizado el `NotificationController` para que las notificaciones manuales solo se envíen a usuarios relacionados con las entidades seleccionadas:

#### Antes:
```php
// ❌ Enviaba a TODOS los usuarios con token FCM
$allUsersWithTokens = User::whereNotNull('fcm_token')->get();
```

#### Después:
```php
// ✅ Solo envía a usuarios de las entidades seleccionadas
$relevantUsers = $this->getUsersFromEntities($selectedEntityIds);
```

#### Método Agregado:

- `getUsersFromEntities($entityIds)`: Obtiene managers y sellers de las entidades seleccionadas

## 🔄 Flujo de Notificaciones

### Notificaciones Automáticas (Observer)

```
1. Evento en Participación (asignación, venta, devolución, etc.)
   ↓
2. Observer detecta el cambio
   ↓
3. Se registra en el log de actividad
   ↓
4. Se identifican usuarios relevantes:
   - Manager de la entidad
   - Vendedor(es) involucrado(s)
   ↓
5. Se envía notificación Firebase a cada usuario
   ↓
6. Se registra el resultado en logs
```

### Notificaciones Manuales (Controller)

```
1. Usuario crea notificación en el panel
   ↓
2. Selecciona entidades destino
   ↓
3. Se identifican usuarios de esas entidades:
   - Managers de las entidades
   - Sellers vinculados a usuarios
   ↓
4. Se envía notificación Firebase a cada usuario
   ↓
5. Se registra en la base de datos
```

## 📱 Contenido de las Notificaciones

Cada notificación incluye:

### Título y Cuerpo
- **Asignación**: "📋 Participación Asignada - Se te ha asignado la participación #XXX"
- **Venta**: "✅ Participación Vendida - La participación #XXX ha sido vendida"
- **Devolución**: "↩️ Participación Devuelta - La participación #XXX ha sido devuelta"
- **Anulación**: "❌ Participación Anulada - La participación #XXX ha sido anulada"

### Datos Adicionales (payload)
```json
{
  "type": "participation_update",
  "event": "assigned|sold|returned|cancelled",
  "participation_id": 123,
  "participation_code": "ABC123",
  "entity_id": 456,
  "user_id": 789,
  "user_role": "manager|seller",
  "timestamp": "2025-10-20T..."
}
```

## 🔧 Configuración de Usuarios

Para que un usuario reciba notificaciones debe:

1. **Tener un token FCM registrado** (`fcm_token` en la tabla `users`)
2. **Estar vinculado a una entidad** como:
   - **Manager**: Tabla `managers` con `user_id` y `entity_id`
   - **Seller**: Tabla `sellers` con `user_id` y `entity_id`

### Tipos de Sellers

- **Sellers vinculados**: Tienen `user_id` > 0 → Reciben notificaciones
- **Sellers externos**: Tienen `seller_type = 'externo'` → NO reciben notificaciones

## 📊 Logs de Seguimiento

El sistema genera logs detallados para seguimiento:

```
📤 Enviando notificación: assigned
  Participation ID: 123
  Participation Code: ABC123
  Usuarios a notificar: 2

  📤 Enviando a: Juan Pérez (manager)
  ✅ Notificación enviada a Juan Pérez (manager)
  
  📤 Enviando a: María García (seller)
  ✅ Notificación enviada a María García (seller)
```

## ⚠️ Consideraciones Importantes

1. **Deduplicación**: Si un usuario es manager Y seller de la misma entidad, solo recibe UNA notificación
2. **Sellers externos**: No reciben notificaciones (no tienen usuario vinculado)
3. **Tokens válidos**: Solo se envían notificaciones a usuarios con `fcm_token` activo
4. **Manejo de errores**: Los errores de envío se registran pero no bloquean el proceso

## 🧪 Pruebas

### Probar Notificaciones Automáticas:

1. Asignar una participación a un vendedor
2. Vender una participación
3. Devolver una participación
4. Anular una participación

### Probar Notificaciones Manuales:

1. Ir al panel de notificaciones
2. Seleccionar entidad(es) destino
3. Escribir mensaje
4. Enviar
5. Verificar que solo reciben usuarios de esas entidades

### Verificar Logs:

```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Limpiar logs
> storage/logs/laravel.log
```

## 🎉 Beneficios

1. ✅ **Privacidad**: Los usuarios solo reciben notificaciones relevantes para ellos
2. ✅ **Eficiencia**: No se saturan los usuarios con notificaciones innecesarias
3. ✅ **Contexto**: Cada notificación incluye información del rol del usuario
4. ✅ **Trazabilidad**: Logs completos de todas las notificaciones enviadas
5. ✅ **Escalabilidad**: Sistema preparado para manejar múltiples entidades y usuarios

## 📝 Archivos Modificados

1. `app/Observers/ParticipationObserver.php` - Notificaciones automáticas
2. `app/Http/Controllers/NotificationController.php` - Notificaciones manuales
3. `app/Services/FirebaseServiceModern.php` - Servicio de Firebase (sin cambios)

---

## 🐛 Corrección de Errores

### Error: "Array to string conversion"

**Problema**: Firebase solo acepta valores simples (strings, números) en el array de datos de la notificación. No se pueden pasar arrays anidados.

**Solución**: 
- Convertir arrays a strings usando `implode()`: `entity_ids` → `"1,2,3"`
- Convertir números a strings: `(string)$user->id`
- Todos los campos de datos ahora son strings o números simples

**Archivos corregidos**:
- `app/Http/Controllers/NotificationController.php` - Líneas 252-257
- `app/Observers/ParticipationObserver.php` - Líneas 340, 474-477

---

**Fecha de implementación**: 20 de octubre de 2025
**Estado**: ✅ Completado, corregido y listo para pruebas

