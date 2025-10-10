<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'entity_id',
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
        'seller_type'
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
     * Relación con Entity
     */
    public function entity()
    {
        return $this->belongsTo(Entity::class);
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
        if ($this->seller_type === 'externo') {
            return $this->attributes['status'] ? 'Activo' : 'Inactivo';
        }
        return $this->user && $this->user->status ? 'Activo' : 'Inactivo';
    }

    public function getStatusClassAttribute()
    {
        if ($this->seller_type === 'externo') {
            return $this->attributes['status'] ? 'success' : 'danger';
        }
        return $this->user && $this->user->status ? 'success' : 'danger';
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
}
