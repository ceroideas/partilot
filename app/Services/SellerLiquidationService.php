<?php

namespace App\Services;

use App\Models\Participation;
use App\Models\Seller;
use App\Models\SellerSettlement;

class SellerLiquidationService
{
    /**
     * Deuda pendiente total por vendedor (suma de todos los sorteos).
     *
     * @return array<int, float>
     */
    public function getPendingLiquidationBySellers(array $sellerIds): array
    {
        if ($sellerIds === []) {
            return [];
        }

        $totalsBySellerLottery = $this->computeTotalsBySellerLottery($sellerIds);

        $paidBySellerLottery = SellerSettlement::query()
            ->whereIn('seller_id', $sellerIds)
            ->selectRaw('seller_id, lottery_id, SUM(paid_amount) as total_paid')
            ->groupBy('seller_id', 'lottery_id')
            ->get()
            ->keyBy(fn ($row) => $row->seller_id.':'.$row->lottery_id);

        $result = array_fill_keys($sellerIds, 0.0);

        foreach ($totalsBySellerLottery as $key => $totalToLiquidate) {
            [$sellerId] = array_map('intval', explode(':', $key, 2));
            $totalPaid = (float) ($paidBySellerLottery[$key]->total_paid ?? 0);
            $pending = $totalToLiquidate - $totalPaid;
            if ($pending > 0) {
                $result[$sellerId] = ($result[$sellerId] ?? 0) + $pending;
            }
        }

        return $result;
    }

    /**
     * Deuda pendiente de liquidación de vendedores de una entidad para un sorteo.
     */
    public function sumPendingLiquidationForEntityLottery(int $entityId, int $lotteryId): float
    {
        $sellerIds = Seller::query()
            ->whereHas('entities', fn ($q) => $q->where('entities.id', $entityId))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($sellerIds === []) {
            return 0.0;
        }

        return array_sum($this->getPendingLiquidationBySellerLottery($sellerIds, $lotteryId));
    }

    public function hasPendingSellerLiquidationForEntityLottery(int $entityId, int $lotteryId): bool
    {
        return $this->sumPendingLiquidationForEntityLottery($entityId, $lotteryId) > 0;
    }

    /**
     * @return array<int, float> seller_id => importe pendiente del sorteo
     */
    public function getPendingLiquidationBySellerLottery(array $sellerIds, int $lotteryId): array
    {
        if ($sellerIds === []) {
            return [];
        }

        $totalsBySellerLottery = $this->computeTotalsBySellerLottery($sellerIds, $lotteryId);

        $paidBySellerLottery = SellerSettlement::query()
            ->whereIn('seller_id', $sellerIds)
            ->where('lottery_id', $lotteryId)
            ->selectRaw('seller_id, lottery_id, SUM(paid_amount) as total_paid')
            ->groupBy('seller_id', 'lottery_id')
            ->get()
            ->keyBy(fn ($row) => $row->seller_id.':'.$row->lottery_id);

        $result = array_fill_keys($sellerIds, 0.0);

        foreach ($totalsBySellerLottery as $key => $totalToLiquidate) {
            [$sellerId] = array_map('intval', explode(':', $key, 2));
            $totalPaid = (float) ($paidBySellerLottery[$key]->total_paid ?? 0);
            $pending = $totalToLiquidate - $totalPaid;
            if ($pending > 0) {
                $result[$sellerId] = ($result[$sellerId] ?? 0) + $pending;
            }
        }

        return $result;
    }

    /**
     * @return array<string, float> clave "sellerId:lotteryId" => importe a liquidar
     */
    private function computeTotalsBySellerLottery(array $sellerIds, ?int $lotteryId = null): array
    {
        $totalsBySellerLottery = [];

        foreach ($sellerIds as $sellerId) {
            $participations = $this->settlementEligibleParticipationsQuery((int) $sellerId, $lotteryId)
                ->with(['set.reserve', 'pendingDigitalSales'])
                ->get();

            foreach ($participations as $participation) {
                $participationLotteryId = (int) ($participation->set->reserve->lottery_id ?? 0);
                if ($participationLotteryId <= 0) {
                    continue;
                }

                if ($lotteryId !== null && $participationLotteryId !== $lotteryId) {
                    continue;
                }

                $key = $sellerId.':'.$participationLotteryId;
                $totalsBySellerLottery[$key] = ($totalsBySellerLottery[$key] ?? 0)
                    + (float) ($participation->set->total_participation_amount ?? 0);
            }
        }

        return $totalsBySellerLottery;
    }

    private function settlementEligibleParticipationsQuery(int $sellerId, ?int $lotteryId = null)
    {
        $query = Participation::query()
            ->eligibleForSellerSettlement($sellerId)
            ->with('set');

        if ($lotteryId) {
            $query->whereHas('set.reserve', fn ($q) => $q->where('lottery_id', $lotteryId));
        }

        return $query;
    }
}
