<?php

namespace App\Console\Commands;

use App\Services\ParticipationGiftService;
use Illuminate\Console\Command;

class SipartExpirePendingParticipationGifts extends Command
{
    protected $signature = 'sipart:expire-pending-gifts';

    protected $description = 'Marca como expirados los regalos no aceptados el día anterior al sorteo';

    public function handle(ParticipationGiftService $service): int
    {
        $count = $service->expirePendingBeforeDraw();
        $this->info("Regalos expirados: {$count}");

        return self::SUCCESS;
    }
}
