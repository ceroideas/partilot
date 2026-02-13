<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use HasFactory;

    protected $fillable = [
        'administration_id',
        'image',
        'name',
        'province',
        'city',
        'postal_code',
        'address',
        'nif_cif',
        'phone',
        'email',
        'comments',
        'status'
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /**
     * Relación con Administration
     */
    public function administration()
    {
        return $this->belongsTo(Administration::class);
    }

    /**
     * Relación con Manager (singular - devuelve el gestor principal)
     */
    public function manager()
    {
        return $this->hasOne(Manager::class,'entity_id','id')->where('is_primary', true);
    }

    /**
     * Relación con Managers (plural)
     */
    public function managers()
    {
        return $this->hasMany(Manager::class,'entity_id','id');
    }

    /**
     * Relación con Reservas
     */
    public function reserves()
    {
        return $this->hasMany(Reserve::class);
    }

    /**
     * Relación con los resultados de escrutinio
     */
    public function scrutinyResults()
    {
        return $this->hasMany(ScrutinyEntityResult::class);
    }

    /**
     * Relación con Sellers (Many to Many)
     */
    public function sellers()
    {
        return $this->belongsToMany(Seller::class, 'entity_seller')
            ->withTimestamps();
    }

    /**
     * Obtener el estado como texto
     */
    public function getStatusTextAttribute()
    {
        if ($this->status === null || $this->status === -1) {
            return 'Pendiente';
        } elseif ($this->status == 1) {
            return 'Activo';
        } else {
            return 'Inactivo';
        }
    }

    /**
     * Obtener el estado como clase CSS
     */
    public function getStatusClassAttribute()
    {
        if ($this->status === null || $this->status === -1) {
            return 'secondary';
        } elseif ($this->status == 1) {
            return 'success';
        } else {
            return 'danger';
        }
    }

    /**
     * Scope para filtrar entidades accesibles por usuario.
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

        return $query->whereIn('id', $entityIds);
    }
}
