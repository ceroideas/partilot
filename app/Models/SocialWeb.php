<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SocialWeb extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'title',
        'description',
        'banner_image',
        'small_image',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Scope para filtrar webs sociales accesibles por usuario.
     */
    public function scopeForUser($query, User $user)
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        $entityIds = $user->accessibleEntityIds();

        if (empty($entityIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('entity_id', $entityIds);
    }
}
