<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotteryDeadlineClosureLog extends Model
{
    public const STATUS_COMPLETED = 'completed';

    public const STATUS_SKIPPED_NO_PENDING = 'skipped_no_pending';

    public const STATUS_SKIPPED_SPECIAL_PRIZE = 'skipped_special_prize';

    public const STATUS_SKIPPED_ALREADY_CLOSED = 'skipped_already_closed';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'entity_id',
        'lottery_id',
        'effective_deadline',
        'devolution_id',
        'participations_sold',
        'participations_returned_digital',
        'total_liquidation',
        'status',
        'message',
        'processed_at',
    ];

    protected $casts = [
        'effective_deadline' => 'date',
        'participations_sold' => 'integer',
        'participations_returned_digital' => 'integer',
        'total_liquidation' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class);
    }

    public function devolution(): BelongsTo
    {
        return $this->belongsTo(Devolution::class);
    }
}
