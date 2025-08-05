<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image',
        'name',
        'last_name',
        'last_name2',
        'nif_cif',
        'birthday',
        'email',
        'phone',
        'comment',
        'status',
        'entity_id'
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /**
     * Relación con User (opcional)
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
     * Obtener el nombre completo del vendedor
     */
    public function getFullNameAttribute()
    {
        $fullName = $this->name ?? '';
        if ($this->last_name) {
            $fullName .= ' ' . $this->last_name;
        }
        if ($this->last_name2) {
            $fullName .= ' ' . $this->last_name2;
        }
        return trim($fullName);
    }

    /**
     * Obtener el estado como texto
     */
    public function getStatusTextAttribute()
    {
        return $this->status == 1 ? 'Activo' : 'Inactivo';
    }

    /**
     * Obtener el estado como clase CSS
     */
    public function getStatusClassAttribute()
    {
        return $this->status == 1 ? 'success' : 'danger';
    }
}
