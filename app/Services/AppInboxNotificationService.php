<?php

namespace App\Services;

use App\Models\Entity;
use App\Models\Lottery;
use App\Models\Manager;
use App\Models\Notification;
use App\Models\Participation;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Notificaciones persistidas para la bandeja de la app móvil + push FCM con notification_id.
 */
class AppInboxNotificationService
{
    public function __construct(
        protected FirebaseServiceModern $firebase
    ) {}

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
     * IDs de usuarios con participaciones en un sorteo (cartera digital y/o venta vinculada a usuario vendedor).
     *
     * @return list<int>
     */
    public function recipientUserIdsForLottery(Lottery $lottery): array
    {
        $ids = [];

        $participations = Participation::query()
            ->whereHas('set.reserve', fn ($q) => $q->where('lottery_id', $lottery->id))
            ->with(['seller:id,user_id'])
            ->get(['id', 'buyer_name', 'seller_id']);

        foreach ($participations as $p) {
            $bn = $p->buyer_name;
            if ($bn !== null && $bn !== '' && ctype_digit((string) $bn)) {
                $ids[] = (int) $bn;
            }
            if ($p->seller_id && $p->seller && (int) $p->seller->user_id > 0) {
                $ids[] = (int) $p->seller->user_id;
            }
        }

        return array_values(array_unique(array_filter($ids)));
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
        array $meta = [],
        bool $sendPush = true
    ): Notification {
        $notification = Notification::create([
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

        if ($sendPush) {
            $this->sendPushForNotification($notification);
        }

        return $notification;
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

    protected function sendPushForNotification(Notification $notification): void
    {
        if (! $notification->recipient_user_id) {
            return;
        }

        $user = User::with('fcmTokens')->find($notification->recipient_user_id);
        if (! $user || $user->shouldExcludeFromOperationalPushRecipients() || $user->fcmTokens->isEmpty()) {
            return;
        }

        $body = Str::limit(strip_tags((string) $notification->message), 180);

        foreach ($user->fcmTokens as $device) {
            try {
                $this->firebase->sendToDevice(
                    $device->token,
                    $notification->title,
                    $body,
                    [
                        'type' => 'inbox_notification',
                        'notification_id' => (string) $notification->id,
                        'kind' => (string) ($notification->kind ?? ''),
                        'platform' => (string) $device->platform,
                    ]
                );
            } catch (\Throwable $e) {
                \Log::warning('FCM inbox notification_id='.$notification->id.': '.$e->getMessage());
            }
        }
    }
}
