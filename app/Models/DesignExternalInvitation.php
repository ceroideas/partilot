<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Entity;
use App\Models\Lottery;
use App\Models\Set;

class DesignExternalInvitation extends Model
{
    protected $table = 'design_external_invitations';

    protected $fillable = [
        'entity_id',
        'lottery_id',
        'set_id',
        'created_by_user_id',
        'comment',
        'email',
        'token',
        'status',
        'sent_at',
        'design_format_id',
        'orden_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT = 'sent';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class);
    }

    public function set(): BelongsTo
    {
        return $this->belongsTo(Set::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_user_id');
    }

    public function designFormat(): BelongsTo
    {
        return $this->belongsTo(DesignFormat::class, 'design_format_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(DesignExternalInvitationFile::class, 'design_external_invitation_id');
    }

    public static function generateToken(): string
    {
        return Str::random(48);
    }

    public static function generateOrdenId(): string
    {
        return '#EN' . strtoupper(Str::random(4)) . rand(100, 999);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING || $this->status === self::STATUS_SENT;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED && $this->design_format_id;
    }
}
