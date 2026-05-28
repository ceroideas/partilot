<?php

namespace App\Services;

use App\Models\Lottery;
use App\Models\Participation;
use Illuminate\Support\Carbon;

/**
 * Vigencia de participaciones en cartera del comprador: hasta N meses después del sorteo.
 */
class ParticipationWalletValidityService
{
    public function monthsAfterDraw(): int
    {
        return max(1, (int) config('digital_sale.wallet_validity_months_after_draw', 3));
    }

    /**
     * Fecha límite de uso en cartera / vinculación de venta digital pendiente.
     */
    public function validUntilForLottery(?Lottery $lottery): Carbon
    {
        $months = $this->monthsAfterDraw();
        $tz = config('app.timezone');

        if ($lottery?->draw_date) {
            return Carbon::parse($lottery->draw_date, $tz)->endOfDay()->addMonths($months);
        }

        return now($tz)->addMonths($months);
    }

    public function validUntilForParticipation(Participation $participation): Carbon
    {
        $participation->loadMissing('set.reserve.lottery');

        return $this->validUntilForLottery($participation->set?->reserve?->lottery);
    }

    public function isParticipationWalletExpired(Participation $participation): bool
    {
        return now()->gt($this->validUntilForParticipation($participation));
    }

    public function walletValidUntilIso(Participation $participation): string
    {
        return $this->validUntilForParticipation($participation)->toIso8601String();
    }
}
