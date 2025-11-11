<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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

    /**
     * Scope para filtrar administraciones accesibles por usuario.
     */
    public function scopeForUser($query, User $user)
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        $administrationIds = $user->accessibleAdministrationIds();

        if (empty($administrationIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('id', $administrationIds);
    }
}
