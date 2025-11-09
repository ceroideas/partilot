<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'entity_id',
        'province'
    ];

    /**
     * Relación con Entity
     */
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Relación con Sellers (Many to Many)
     */
    public function sellers()
    {
        return $this->belongsToMany(Seller::class, 'group_seller')
            ->withTimestamps();
    }

    /**
     * Obtener el número de vendedores en el grupo
     */
    public function getSellersCountAttribute()
    {
        // Si la relación ya está cargada, usar count() en la colección
        if ($this->relationLoaded('sellers')) {
            return $this->sellers->count();
        }
        // Si no está cargada, hacer la consulta
        return $this->sellers()->count();
    }
}
