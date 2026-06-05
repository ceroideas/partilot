<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotteryDeadlineAdminDecision extends Model
{
    public const DECISION_ASSUME_DEBT = 'assume_debt';

    public const DECISION_ANNUL = 'annul';

    protected $fillable = [
        'entity_id',
        'lottery_id',
        'decision',
        'user_id',
        'notes',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function hasDecision(int $entityId, int $lotteryId): bool
    {
        return static::query()
            ->where('entity_id', $entityId)
            ->where('lottery_id', $lotteryId)
            ->exists();
    }
}
