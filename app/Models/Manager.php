<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    use HasFactory;
    
    protected $fillable = [
        "user_id",
        "entity_id",
        "administration_id",
        "is_primary",
        "permission_sellers",
        "permission_design",
        "permission_statistics",
        "permission_payments",
        "confirmation_token",
        "confirmation_sent_at",
        "status",
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'permission_sellers' => 'boolean',
        'permission_design' => 'boolean',
        'permission_statistics' => 'boolean',
        'permission_payments' => 'boolean',
        'confirmation_sent_at' => 'datetime',
    ];

    /**
     * Relación con User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con Entity
     */
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function administration()
    {
        return $this->belongsTo(Administration::class);
    }
}
