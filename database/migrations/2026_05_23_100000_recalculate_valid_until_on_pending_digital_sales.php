<?php

use App\Models\Lottery;
use App\Models\PendingDigitalSale;
use App\Services\ParticipationWalletValidityService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $validity = app(ParticipationWalletValidityService::class);

        PendingDigitalSale::query()
            ->where('status', PendingDigitalSale::STATUS_PENDING)
            ->whereNotNull('lottery_id')
            ->orderBy('id')
            ->chunkById(100, function ($rows) use ($validity) {
                foreach ($rows as $pending) {
                    $lottery = Lottery::find($pending->lottery_id);
                    if (! $lottery) {
                        continue;
                    }
                    $until = $validity->validUntilForLottery($lottery);
                    PendingDigitalSale::whereKey($pending->id)->update(['valid_until' => $until]);
                }
            });
    }

    public function down(): void
    {
        // No reversible de forma fiable
    }
};
