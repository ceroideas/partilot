<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackgroundTask extends Model
{
    use HasFactory;

    public const TYPE_PARTICIPATION_CREATION = 'participation_creation';
    public const TYPE_PARTICIPATION_ASSIGNMENT = 'participation_assignment';
    public const TYPE_DEVOLUTION = 'devolution';
    public const TYPE_DEVOLUTION_DELETE = 'devolution_delete';

    public const STATUS_PENDING = 'pending';
    public const STATUS_RUNNING = 'running';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'uuid',
        'type',
        'status',
        'requested_by_user_id',
        'entity_id',
        'administration_id',
        'set_id',
        'resource_key',
        'task_hash',
        'payload',
        'progress_total',
        'progress_done',
        'progress_percent',
        'result_summary',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'result_summary' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public static function supportedTypes(): array
    {
        return [
            self::TYPE_PARTICIPATION_CREATION,
            self::TYPE_PARTICIPATION_ASSIGNMENT,
            self::TYPE_DEVOLUTION,
            self::TYPE_DEVOLUTION_DELETE,
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }
}
