<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administration extends Model
{
    use HasFactory;
    
    protected $fillable = [
        "web",
        "name",
        "receiving",
        "society",
        "nif_cif",
        "province",
        "city",
        "postal_code",
        "address",
        "email",
        "phone",
        "account",
        "status",
        "image"
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Relación con Entity
     */
    public function entities()
    {
        return $this->hasMany(Entity::class);
    }

    public function manager()
    {
        return $this->hasOne(Manager::class,'administration_id','id');
    }

    /**
     * Relación con los escrutinios de lotería de esta administración
     */
    public function lotteryScrutinies()
    {
        return $this->hasMany(AdministrationLotteryScrutiny::class);
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
