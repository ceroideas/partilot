<?php

namespace App\Services;

use App\Mail\DigitalPurchaseConfirmationMail;
use App\Mail\ParticipationWalletLinkedMail;
use App\Models\Participation;
use App\Models\PendingDigitalSale;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DigitalParticipationNotificationService
{
    public function sendPurchaseConfirmation(User $buyer, Collection|array $participations, float $totalAmount, array $context = []): void
    {
        if (! $buyer->email) {
            return;
        }

        $items = collect($participations)->map(function (Participation $p) {
            return [
                'code' => $p->display_participation_code,
                'entity' => $p->set?->entity?->name ?? '',
            ];
        })->values()->all();

        try {
            app(CommunicationEmailService::class)->sendAndLog(
                recipientEmail: (string) $buyer->email,
                recipientRole: 'usuario',
                recipientUser: $buyer,
                messageType: 'digital_purchase_confirmation',
                templateKey: null,
                mailClass: DigitalPurchaseConfirmationMail::class,
                mailPayload: [
                    'buyer_id' => $buyer->id,
                    'items' => $items,
                    'total_amount' => $totalAmount,
                ],
                context: $context,
            );
        } catch (\Throwable $e) {
            Log::warning('Fallo enviando confirmación compra digital: '.$e->getMessage());
        }
    }

    public function sendWalletLinked(User $user, Participation $participation, string $source = 'wallet_link'): void
    {
        if (! $user->email) {
            return;
        }

        try {
            app(CommunicationEmailService::class)->sendAndLog(
                recipientEmail: (string) $user->email,
                recipientRole: 'usuario',
                recipientUser: $user,
                messageType: 'participation_wallet_linked',
                templateKey: null,
                mailClass: ParticipationWalletLinkedMail::class,
                mailPayload: [
                    'user_id' => $user->id,
                    'participation_id' => $participation->id,
                ],
                context: ['source' => $source],
            );
        } catch (\Throwable $e) {
            Log::warning('Fallo enviando email vinculación cartera: '.$e->getMessage());
        }
    }

    public function sendPendingClaimed(User $buyer, PendingDigitalSale $pending): void
    {
        $pending->loadMissing(['participations.set.entity', 'entity', 'lottery']);
        $participations = $pending->participations;
        if ($participations->isEmpty()) {
            return;
        }

        $this->sendPurchaseConfirmation(
            $buyer,
            $participations,
            (float) $pending->sale_amount,
            [
                'source' => 'pending_digital_claim',
                'pending_digital_sale_id' => $pending->id,
                'link_code' => $pending->link_code,
            ]
        );
    }
}
