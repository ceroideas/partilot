<?php

namespace App\Jobs;

use App\Models\Lottery;
use App\Models\Participation;
use App\Models\User;
use App\Services\AppInboxNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyLotteryResultsPublishedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $lotteryId,
        public ?int $publisherUserId = null
    ) {}

    public function handle(AppInboxNotificationService $inbox): void
    {
        $lottery = Lottery::find($this->lotteryId);
        if (! $lottery) {
            return;
        }

        $userIds = $inbox->recipientUserIdsForLottery($lottery);
        if ($userIds === []) {
            return;
        }

        $senderId = $this->publisherUserId
            ?: User::query()->where('role', User::ROLE_SUPER_ADMIN)->orderBy('id')->value('id')
            ?: 1;

        foreach ($userIds as $uid) {
            $p = Participation::query()
                ->whereHas('set.reserve', fn ($q) => $q->where('lottery_id', $lottery->id))
                ->where(function ($q) use ($uid) {
                    $q->where('buyer_name', (string) $uid)
                        ->orWhereHas('seller', fn ($s) => $s->where('user_id', $uid));
                })
                ->with('set.entity')
                ->first();

            $entity = $p?->set?->entity;
            $entityId = $entity ? (int) $entity->id : null;
            $adminId = $entity && $entity->administration_id ? (int) $entity->administration_id : null;

            $inbox->notifyUser(
                $uid,
                $entityId,
                $adminId,
                (int) $senderId,
                'resultados_sorteo',
                'Resultados: '.$lottery->name,
                'Ya están publicados los resultados de este sorteo. Consulta la app para ver los premios.',
                [
                    'lottery_id' => $lottery->id,
                    'rol_context' => 'usuario',
                ]
            );
        }
    }
}
