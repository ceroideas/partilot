<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingEntityManagerInvitation extends Model
{
    protected $fillable = [
        'email',
        'entity_id',
        'is_primary',
        'permission_sellers',
        'permission_design',
        'permission_statistics',
        'permission_payments',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'permission_sellers' => 'boolean',
        'permission_design' => 'boolean',
        'permission_statistics' => 'boolean',
        'permission_payments' => 'boolean',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public static function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }
}
