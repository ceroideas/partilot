<?php

namespace App\Services;

use App\Models\Entity;
use App\Models\Manager;
use App\Models\Notification;
use App\Models\Seller;
use App\Models\User;

/**
 * Notificaciones persistidas para la bandeja de la app móvil + datos para push FCM.
 */
class AppInboxNotificationService
{
    /**
     * Resuelve un usuario emisor válido para FK sender_id (gestor de entidad o primer superadmin).
     */
    public function resolveSenderIdForEntity(int $entityId): ?int
    {
        $uid = Manager::query()->where('entity_id', $entityId)->orderBy('id')->value('user_id');
        if ($uid) {
            return (int) $uid;
        }

        return User::query()->where('role', User::ROLE_SUPER_ADMIN)->orderBy('id')->value('id');
    }

    /**
     * Notificación dirigida a un usuario concreto (p. ej. vendedor con cuenta SIPART).
     */
    public function notifyUser(
        int $recipientUserId,
        ?int $entityId,
        ?int $administrationId,
        int $senderId,
        string $kind,
        string $title,
        string $message,
        array $meta = []
    ): Notification {
        return Notification::create([
            'recipient_user_id' => $recipientUserId,
            'entity_id' => $entityId,
            'administration_id' => $administrationId,
            'sender_id' => $senderId,
            'title' => $title,
            'message' => $message,
            'kind' => $kind,
            'meta' => $meta,
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Tras asignar participaciones a un vendedor con usuario vinculado.
     */
    public function notifyParticipationAssigned(Seller $seller, int $assignedCount, ?string $lotteryHint): void
    {
        if ($assignedCount <= 0 || ! $seller->user_id) {
            return;
        }

        $seller->loadMissing('entities');
        $entity = $seller->entities->first();
        if (! $entity instanceof Entity) {
            return;
        }

        $senderId = $this->resolveSenderIdForEntity((int) $entity->id);
        if (! $senderId) {
            return;
        }

        $msg = $assignedCount === 1
            ? 'Se te ha asignado 1 nueva participación.'
            : "Se te han asignado {$assignedCount} participaciones.";
        if ($lotteryHint) {
            $msg .= ' '.$lotteryHint;
        }

        $this->notifyUser(
            (int) $seller->user_id,
            (int) $entity->id,
            $entity->administration_id ? (int) $entity->administration_id : null,
            $senderId,
            'asignacion_participaciones',
            $entity->name,
            $msg,
            [
                'rol_context' => 'vendedor',
                'seller_id' => $seller->id,
                'entity_name' => $entity->name,
            ]
        );
    }
}
