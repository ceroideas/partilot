<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotteryDeadlineReminderLog extends Model
{
    public const CHANNEL_EMAIL_ENTITY = 'email_entity';

    public const CHANNEL_EMAIL_ADMINISTRATION = 'email_administration';

    public const CHANNEL_EMAIL_MANAGER = 'email_manager';

    public const CHANNEL_MODAL = 'modal';

    protected $fillable = [
        'entity_id',
        'lottery_id',
        'days_before',
        'channel',
        'recipient',
        'reminded_on',
        'sent_at',
    ];

    protected $casts = [
        'days_before' => 'integer',
        'reminded_on' => 'date',
        'sent_at' => 'datetime',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class);
    }
}
