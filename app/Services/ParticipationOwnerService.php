<?php

namespace App\Services;

use App\Models\Participation;
use App\Models\User;

/**
 * Utilidades sobre titular en cartera: buyer_name = id de usuario (string).
 * No guardar el nombre en buyer_name; resolver con Participation::walletOwner().
 */
class ParticipationOwnerService
{
    /**
     * Vincula la participación a un usuario (buyer_name = id, email/teléfono auxiliares).
     */
    public static function assignOwner(Participation $participation, User $user): void
    {
        $participation->buyer_name = (string) $user->id;
        $participation->buyer_email = $user->email;
        if ($user->phone) {
            $participation->buyer_phone = $user->phone;
        }
    }

    public static function resolveOwnerUser(Participation $participation): ?User
    {
        if (! $participation->buyerNameIsWalletUserId()) {
            return null;
        }

        if ($participation->relationLoaded('walletOwner')) {
            return $participation->walletOwner;
        }

        return $participation->walletOwner;
    }

    public static function ownerDisplayName(Participation $participation): ?string
    {
        $user = self::resolveOwnerUser($participation);
        if ($user) {
            return trim($user->name.' '.($user->last_name ?? '')) ?: $user->email;
        }

        // Texto libre en ventas físicas sin usuario app (no es id numérico).
        $key = trim((string) ($participation->buyer_name ?? ''));
        if ($key !== '' && ! ctype_digit($key)) {
            return $key;
        }

        return null;
    }

    /**
     * Metadatos de titular para participation_activity_logs (solo lectura en UI).
     */
    public static function ownerMetadata(Participation $participation): array
    {
        $user = self::resolveOwnerUser($participation);

        return array_filter([
            'owner_user_id' => $user?->id,
            'owner_user_name' => self::ownerDisplayName($participation),
            'owner_user_email' => $participation->buyer_email ?? $user?->email,
        ], fn ($v) => $v !== null && $v !== '');
    }
}
