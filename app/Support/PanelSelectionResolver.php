<?php

namespace App\Support;

use App\Models\Administration;
use App\Models\Entity;
use App\Models\User;

class PanelSelectionResolver
{
    /**
     * Entidad única implícita cuando el usuario no debe elegir (cuenta entidad, gestor con una sola entidad, etc.).
     */
    public static function implicitEntityId(User $user, ?string $permission = null): ?int
    {
        if ($user->isSuperAdmin()) {
            return null;
        }

        $ids = $permission !== null
            ? $user->accessibleEntityIdsByPermission($permission)
            : $user->accessibleEntityIds();

        $ids = array_values(array_unique(array_map('intval', $ids)));

        return count($ids) === 1 ? $ids[0] : null;
    }

    public static function implicitAdministrationId(User $user): ?int
    {
        if ($user->isSuperAdmin()) {
            return null;
        }

        if ($user->panel_account_type === 'administration' && $user->panel_account_id) {
            return (int) $user->panel_account_id;
        }

        if ($user->isAdministration()) {
            $ids = array_values(array_unique(array_map('intval', $user->accessibleAdministrationIds())));

            return count($ids) === 1 ? $ids[0] : null;
        }

        return null;
    }

    public static function resolveEntity(User $user, ?string $permission = null, bool $requireActive = true): ?Entity
    {
        $entityId = self::implicitEntityId($user, $permission);
        if (! $entityId) {
            return null;
        }

        $entity = Entity::with(['administration', 'manager'])
            ->forUser($user)
            ->find($entityId);

        if (! $entity) {
            return null;
        }

        if ($requireActive && (int) $entity->status !== 1) {
            return null;
        }

        return $entity;
    }

    public static function resolveAdministration(User $user): ?Administration
    {
        $administrationId = self::implicitAdministrationId($user);
        if (! $administrationId) {
            return null;
        }

        return Administration::with('manager.user')
            ->forUser($user)
            ->find($administrationId);
    }
}
