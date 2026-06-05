<?php

namespace App\Support;

use App\Models\User;

/**
 * Permisos de UI/acciones del módulo Sorteos por rol de panel.
 */
class LotteryPanelAccess
{
    public static function for(?User $user): array
    {
        if (! $user) {
            return self::defaults();
        }

        $isSuperAdmin = $user->isSuperAdmin();
        $isAdministration = $user->isAdministration() && ! $isSuperAdmin;
        $isEntity = $user->isEntity() && ! $isSuperAdmin && ! $isAdministration;

        return [
            'canManageLotteries' => $isSuperAdmin,
            'canEditLotteryFull' => $isSuperAdmin,
            'canEditAdminDeadlineOnly' => $isAdministration,
            'canRunScrutiny' => $isSuperAdmin,
            'canViewLotteryTypes' => $isSuperAdmin,
            'canViewResultsLists' => $isSuperAdmin || $isAdministration,
            'canViewEntityPrizesOnly' => $isEntity || $user->isEntityPanelReadOnly(),
            'isEntityRole' => $isEntity,
        ];
    }

    private static function defaults(): array
    {
        return [
            'canManageLotteries' => false,
            'canEditLotteryFull' => false,
            'canEditAdminDeadlineOnly' => false,
            'canRunScrutiny' => false,
            'canViewLotteryTypes' => false,
            'canViewResultsLists' => false,
            'canViewEntityPrizesOnly' => false,
            'isEntityRole' => false,
        ];
    }
}
