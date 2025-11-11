<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'name',
        'last_name',
        'last_name2',
        'nif_cif',
        'birthday',
        'phone',
        'comment',
        'image',
        'status',
        'seller_type',
        'group_name',
        'group_color',
        'group_priority'
    ];

    protected $casts = [
        'status' => 'boolean',
        'birthday' => 'date',
    ];

    /**
     * Relación con User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con Entities (Many to Many)
     */
    public function entities()
    {
        return $this->belongsToMany(Entity::class, 'entity_seller')
            ->withTimestamps();
    }

    /**
     * Relación con Groups (Many to Many)
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_seller')
            ->withTimestamps();
    }

    /**
     * Obtener la entidad principal (primera entidad del vendedor)
     * Útil para mostrar datos cuando no hay contexto específico
     */
    public function getPrimaryEntity()
    {
        return $this->entities->first();
    }

    /**
     * Verificar si el vendedor pertenece a una entidad específica
     */
    public function belongsToEntity($entityId)
    {
        return $this->entities->contains('id', $entityId);
    }


    /**
     * Accesores para obtener datos (prioridad: datos directos > usuario vinculado)
     */
    public function getFullNameAttribute()
    {
        if ($this->seller_type === 'externo') {
            $name = trim(($this->attributes['name'] ?? '') . ' ' . ($this->attributes['last_name'] ?? '') . ' ' . ($this->attributes['last_name2'] ?? ''));
            return !empty($name) ? $name : 'Sin nombre';
        }
        return $this->user ? $this->user->full_name : 'Sin nombre';
    }

    public function getStatusTextAttribute()
    {
        // if ($this->seller_type === 'externo') {
            return $this->attributes['status'] ? 'Activo' : 'Inactivo';
        // }
        // return $this->user && $this->user->status ? 'Activo' : 'Inactivo';
    }

    public function getStatusClassAttribute()
    {
        // if ($this->seller_type === 'externo') {
            return $this->attributes['status'] ? 'success' : 'danger';
        // }
        // return $this->user && $this->user->status ? 'success' : 'danger';
    }

    /**
     * Métodos para obtener datos según el tipo de vendedor
     */
    public function getDisplayNameAttribute()
    {
        if ($this->seller_type === 'externo') {
            return $this->attributes['name'] ?? 'Sin nombre';
        }
        return $this->user ? $this->user->name : 'Sin nombre';
    }

    public function getDisplayLastNameAttribute()
    {
        if ($this->seller_type === 'externo') {
            return $this->attributes['last_name'] ?? '';
        }
        return $this->user ? $this->user->last_name : '';
    }

    public function getDisplayEmailAttribute()
    {
        if ($this->seller_type === 'externo') {
            return $this->attributes['email'] ?? '';
        }
        return $this->user ? $this->user->email : '';
    }

    public function getDisplayPhoneAttribute()
    {
        if ($this->seller_type === 'externo') {
            return $this->attributes['phone'] ?? '';
        }
        return $this->user ? $this->user->phone : '';
    }

    /**
     * Verificar si el vendedor está vinculado a un usuario
     */
    public function isLinkedToUser()
    {
        return $this->user_id > 0;
    }

    /**
     * Verificar si el vendedor está pendiente de vinculación
     */
    public function isPendingLink()
    {
        return $this->user_id === 0; // Tanto PARTILOT pendientes como EXTERNO
    }

    /**
     * Obtener el color del grupo o un color por defecto
     */
    public function getGroupColorAttribute($value)
    {
        return $value ?: '#6c757d'; // Color gris por defecto
    }

    /**
     * Obtener el nombre del grupo o un nombre por defecto
     */
    public function getGroupNameAttribute($value)
    {
        return $value ?: 'Sin grupo';
    }

    /**
     * Scope para filtrar por grupo
     */
    public function scopeByGroup($query, $groupName)
    {
        return $query->where('group_name', $groupName);
    }

    /**
     * Scope para ordenar por prioridad de grupo
     */
    public function scopeOrderByGroup($query)
    {
        return $query->orderBy('group_priority', 'desc')
                    ->orderBy('group_name', 'asc')
                    ->orderBy('name', 'asc');
    }

    /**
     * Scope para filtrar vendedores accesibles por usuario.
     */
    public function scopeForUser($query, User $user)
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        $sellerIds = $user->accessibleSellerIds();

        if (empty($sellerIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('id', $sellerIds);
    }
}
