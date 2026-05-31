<?php

namespace App\Console\Commands;

use App\Models\ParticipationCollection;
use Illuminate\Console\Command;

class SipartExpireUnverifiedTransferCollections extends Command
{
    protected $signature = 'sipart:expire-unverified-collections';

    protected $description = 'Expira solicitudes de cobro por transferencia no confirmadas por email';

    public function handle(): int
    {
        $collections = ParticipationCollection::query()
            ->where('status', ParticipationCollection::STATUS_PENDING_VERIFICATION)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($collections as $collection) {
            $collection->markAsExpired();
            $count++;
        }

        $this->info("Solicitudes de cobro expiradas: {$count}");

        return self::SUCCESS;
    }
}
