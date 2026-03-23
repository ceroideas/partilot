<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PanelAccessToken extends Model
{
    protected $fillable = [
        'user_id',
        'token_hash',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Crea un token de un solo uso y devuelve el valor en claro para la URL.
     */
    public static function issueForUser(User $user, ?\DateTimeInterface $expiresAt = null): string
    {
        $plain = Str::random(64);
        $expiresAt = $expiresAt ?? now()->addDays((int) config('partilot.panel_magic_link_ttl_days', 7));

        self::query()->create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $plain),
            'expires_at' => $expiresAt,
        ]);

        return $plain;
    }

    public static function findValidForPlain(string $plain): ?self
    {
        if (strlen($plain) < 32) {
            return null;
        }

        $hash = hash('sha256', $plain);

        return self::query()
            ->where('token_hash', $hash)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();
    }

    public function markUsed(): void
    {
        $this->update(['used_at' => now()]);
    }
}
