<?php

namespace App\Services;

use App\Mail\DigitalSaleRegistrationInviteMail;
use App\Models\Participation;
use App\Models\PendingDigitalSale;
use App\Models\Seller;
use App\Models\Set;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PendingDigitalSaleService
{
    public function validUntilFromConfig(): Carbon
    {
        $days = (int) config('digital_sale.hold_days', 0);
        if ($days > 0) {
            return now()->addDays($days);
        }

        $hours = (int) config('digital_sale.hold_hours', 72);

        return now()->addHours(max(1, $hours));
    }

    /**
     * @return \Illuminate\Support\Collection<int, Participation>
     */
    public function selectDigitalParticipations(
        Seller $seller,
        int $quantity,
        ?int $setId,
        ?int $entityId,
        ?int $lotteryId
    ) {
        $this->releaseExpiredForDigitalContext($entityId, $lotteryId, $setId);

        if ($setId) {
            $set = Set::with('reserve')->findOrFail($setId);
            if (($set->digital_participations ?? 0) <= 0) {
                throw new \InvalidArgumentException('Este set no es de participaciones digitales.');
            }

            return Participation::where('set_id', $setId)
                ->where('seller_id', $seller->id)
                ->where('status', 'asignada')
                ->orderBy('participation_number')
                ->limit($quantity)
                ->get();
        }

        if (! $entityId || ! $lotteryId) {
            throw new \InvalidArgumentException('Indica set_id o entity_id + lottery_id.');
        }

        if (! $seller->entities()->where('entities.id', $entityId)->exists()) {
            throw new \InvalidArgumentException('No tienes acceso a esta entidad.');
        }

        $ids = Participation::query()
            ->join('sets', 'participations.set_id', '=', 'sets.id')
            ->join('reserves', 'sets.reserve_id', '=', 'reserves.id')
            ->where('participations.entity_id', $entityId)
            ->where('reserves.lottery_id', $lotteryId)
            ->where('sets.physical_participations', '<=', 0)
            ->whereRaw('sets.digital_participations > 0')
            ->whereRaw("participations.participation_code LIKE '1D/%'")
            ->where('participations.status', 'disponible')
            ->select('participations.id')
            ->orderBy('participations.id')
            ->limit($quantity)
            ->pluck('participations.id');

        return Participation::with('set.reserve')->whereIn('id', $ids)->orderBy('id')->get();
    }

    public function createPendingSale(
        Seller $seller,
        User $sellerUser,
        string $buyerEmail,
        int $quantity,
        ?string $paymentMethod,
        ?int $setId,
        ?int $entityId,
        ?int $lotteryId
    ): PendingDigitalSale {
        $email = PendingDigitalSale::normalizeEmail($buyerEmail);

        if (User::where('email', $email)->exists()) {
            throw new \InvalidArgumentException('El correo ya está registrado. Usa la venta directa.');
        }

        $participations = $this->selectDigitalParticipations($seller, $quantity, $setId, $entityId, $lotteryId);
        if ($participations->count() < $quantity) {
            throw new \InvalidArgumentException(
                'No hay suficientes participaciones digitales disponibles. Disponibles: '.$participations->count()
            );
        }

        $set = $participations->first()->set;
        $pricePer = (float) ($set->played_amount ?? $set->total_participation_amount ?? 0);
        $saleAmount = $participations->count() * $pricePer;

        return DB::transaction(function () use (
            $email,
            $seller,
            $sellerUser,
            $participations,
            $quantity,
            $paymentMethod,
            $setId,
            $entityId,
            $lotteryId,
            $saleAmount,
            $set
        ) {
            $pending = PendingDigitalSale::create([
                'email' => $email,
                'seller_id' => $seller->id,
                'entity_id' => $entityId ?? $set->entity_id ?? $participations->first()->entity_id,
                'lottery_id' => $lotteryId ?? $set->reserve->lottery_id,
                'set_id' => $setId,
                'quantity' => $quantity,
                'sale_amount' => $saleAmount,
                'payment_method' => $paymentMethod,
                'registration_token' => Str::random(64),
                'status' => PendingDigitalSale::STATUS_PENDING,
                'valid_until' => $this->validUntilFromConfig(),
            ]);

            foreach ($participations as $p) {
                $p->update(['status' => 'reserva_venta_digital']);
                $pending->participations()->attach($p->id);
            }

            app(CommunicationEmailService::class)->sendAndLog(
                recipientEmail: $email,
                recipientRole: 'usuario',
                recipientUser: null,
                messageType: 'digital_sale_registration_invite',
                templateKey: null,
                mailClass: DigitalSaleRegistrationInviteMail::class,
                mailPayload: ['pending_digital_sale_id' => $pending->id],
                context: ['pending_digital_sale_id' => $pending->id, 'seller_id' => $seller->id],
            );

            return $pending->fresh(['entity', 'lottery', 'seller']);
        });
    }

    /**
     * Al registrarse un usuario con el mismo email, completar ventas pendientes no caducadas.
     */
    public function completePendingSalesForUser(User $user): int
    {
        $email = PendingDigitalSale::normalizeEmail((string) $user->email);
        $this->releaseExpiredForEmail($email);
        $completed = 0;

        $pendings = PendingDigitalSale::query()
            ->where('email', $email)
            ->pendingNotExpired()
            ->with(['participations.set.reserve', 'seller'])
            ->get();

        foreach ($pendings as $pending) {
            try {
                $this->finalizePendingSale($pending, $user);
                $completed++;
            } catch (\Throwable $e) {
                \Log::error('Error completando venta digital pendiente #'.$pending->id.': '.$e->getMessage());
            }
        }

        return $completed;
    }

    public function finalizePendingSale(PendingDigitalSale $pending, User $buyer): void
    {
        if ($pending->status !== PendingDigitalSale::STATUS_PENDING) {
            return;
        }

        if ($pending->isExpired()) {
            $this->releasePendingSale($pending, PendingDigitalSale::STATUS_EXPIRED);

            return;
        }

        DB::transaction(function () use ($pending, $buyer) {
            $pending->load(['participations.set.reserve', 'seller']);
            $seller = $pending->seller;
            if (! $seller) {
                throw new \RuntimeException('Vendedor no encontrado.');
            }

            $participations = $pending->participations;
            $set = $participations->first()?->set;
            $pricePer = $participations->count() > 0
                ? ((float) $pending->sale_amount / $participations->count())
                : 0;

            foreach ($participations as $p) {
                if ($p->status !== 'reserva_venta_digital') {
                    continue;
                }
                $p->markAsSold($seller->id, $pricePer, [
                    'name' => (string) $buyer->id,
                    'email' => $buyer->email,
                ], $pending->payment_method);
            }

            if ($participations->isNotEmpty() && $set) {
                app(SellerSettlementFromSaleService::class)->recordIfNeeded(
                    $seller,
                    $participations,
                    $set,
                    (float) $pending->sale_amount,
                    $pending->payment_method,
                    (int) ($seller->user_id ?? $buyer->id)
                );
            }

            $pending->update([
                'status' => PendingDigitalSale::STATUS_COMPLETED,
                'completed_at' => now(),
                'completed_user_id' => $buyer->id,
            ]);
        });
    }

    public function releasePendingSale(PendingDigitalSale $pending, string $status = PendingDigitalSale::STATUS_EXPIRED): void
    {
        DB::transaction(function () use ($pending, $status) {
            $pending->load('participations');
            $restoreStatus = $pending->set_id ? 'asignada' : 'disponible';
            foreach ($pending->participations as $p) {
                if ($p->status === 'reserva_venta_digital') {
                    $p->update(['status' => $restoreStatus]);
                }
            }
            $pending->update(['status' => $status]);
        });
    }

    /**
     * Caducidad pasiva: libera reservas vencidas (valid_until) del pool o set consultado.
     * No requiere cron; se invoca al vender, consultar stock o abrir registro.
     */
    public function releaseExpiredForDigitalContext(?int $entityId, ?int $lotteryId, ?int $setId = null): int
    {
        if (! $setId && (! $entityId || ! $lotteryId)) {
            return 0;
        }

        $query = PendingDigitalSale::query()
            ->where('status', PendingDigitalSale::STATUS_PENDING)
            ->where('valid_until', '<', now());

        if ($setId) {
            $query->where('set_id', $setId);
        } else {
            $query->where('entity_id', $entityId)->where('lottery_id', $lotteryId);
        }

        return $this->releasePendingQuery($query);
    }

    /** Libera ventas pendientes caducadas de un email (p. ej. al registrarse). */
    public function releaseExpiredForEmail(string $email): int
    {
        $email = PendingDigitalSale::normalizeEmail($email);

        $query = PendingDigitalSale::query()
            ->where('email', $email)
            ->where('status', PendingDigitalSale::STATUS_PENDING)
            ->where('valid_until', '<', now());

        return $this->releasePendingQuery($query);
    }

    private function releasePendingQuery($query): int
    {
        $released = 0;
        $query->orderBy('id')->chunkById(50, function ($rows) use (&$released) {
            foreach ($rows as $pending) {
                $this->releasePendingSale($pending, PendingDigitalSale::STATUS_EXPIRED);
                $released++;
            }
        });

        return $released;
    }

    public function findValidByToken(string $token): ?PendingDigitalSale
    {
        $pending = PendingDigitalSale::query()
            ->where('registration_token', $token)
            ->where('status', PendingDigitalSale::STATUS_PENDING)
            ->with(['entity', 'lottery'])
            ->first();

        if (! $pending || $pending->isExpired()) {
            if ($pending && $pending->isExpired()) {
                $this->releasePendingSale($pending, PendingDigitalSale::STATUS_EXPIRED);
            }

            return null;
        }

        return $pending;
    }
}
