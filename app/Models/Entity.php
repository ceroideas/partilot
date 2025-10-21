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
        'status' => 'boolean',
    ];

    /**
     * Relación con Administration
     */
    public function administration()
    {
        return $this->belongsTo(Administration::class);
    }

    /**
     * Relación con Manager
     */
    public function manager()
    {
        return $this->hasOne(Manager::class,'entity_id','id');
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
        return $this->status ? 'Activo' : 'Inactivo';
    }

    /**
     * Obtener el estado como clase CSS
     */
    public function getStatusClassAttribute()
    {
        return $this->status ? 'success' : 'danger';
    }
}
