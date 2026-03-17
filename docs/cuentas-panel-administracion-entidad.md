# Cuentas de panel (administración / entidad)

## Modelo

- **Un usuario de panel por administración** y **uno por entidad**, con los mismos datos de contacto que el registro (`email`, nombre comercial + sociedad / nombre entidad, `nif_cif`, `teléfono`, etc.).
- Cada uno tiene `panel_account_type` = `administration` | `entity` y `panel_account_id` = id del registro.
- Además existe un registro en **`managers`** (principal) para que sigan funcionando permisos y consultas por `Manager`.

## Acceso al panel web

- Solo entran **super_admin** y usuarios con `panel_account_*` relleno.
- Los **gestores que no son esa cuenta** (otros `managers`) **no inician sesión** en el panel aunque tengan fila en `managers`.

## Migración existente

Tras `php artisan migrate`, la migración `2026_03_18_150000_create_dedicated_panel_users_for_administrations_entities`:

1. Quita `panel_account_*` a todos los usuarios.
2. Por cada administración con email válido: crea o actualiza el usuario del panel y deja su `Manager` como **principal** de esa administración (el resto de managers de esa administración pasan a no principal).
3. Igual por entidad.

**Contraseña inicial de las cuentas creadas en esa migración:** `PanelMigracion2026!`  
Conviene cambiarla desde la edición de administración / entidad.

## Conflictos de email

- Si el email de la administración ya lo usa **otro usuario que no es gestor de esa administración**, no se crea usuario automáticamente (hay que corregir emails o crear la cuenta a mano).
- Si el email de una entidad ya pertenece a un usuario **panel de administración**, no se crea la cuenta de entidad con ese email (resolver manualmente).
