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
        "admin_number",
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
        'status' => 'integer',
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
        return $this->hasOne(Manager::class,'administration_id','id')->where('is_primary', true);
    }

    /**
     * Nombre para la cuenta de usuario del panel: solo nombre comercial.
     * Si falta, se usa sociedad; si ambos faltan, "Administración".
     */
    public static function panelDisplayNameFromParts(?string $commercialName, ?string $society): string
    {
        $name = trim((string) $commercialName);
        if ($name !== '') {
            return $name;
        }
        $soc = trim((string) $society);

        return $soc !== '' ? $soc : 'Administración';
    }

    /**
     * Usuario de acceso al panel (fijo): receptor (5 dígitos) + 3 últimos del nº administración (Administración de Lotería).
     * Punto de venta mixto (sin número de administración): solo el receptor.
     */
    public static function panelLoginUsernameFromParts(?string $receiving, ?string $adminNumber): string
    {
        $recvDigits = preg_replace('/\D/', '', (string) $receiving);
        $recvDigits = substr(str_pad($recvDigits, 5, '0', STR_PAD_LEFT), -5);

        $adm = trim((string) $adminNumber);
        if ($adm === '') {
            return $recvDigits;
        }

        $numDigits = preg_replace('/\D/', '', $adm);
        $last3 = substr(str_pad($numDigits, 3, '0', STR_PAD_LEFT), -3);

        return $recvDigits.$last3;
    }

    /**
     * Garantizar unicidad de `panel_login_username` en users.
     */
    public static function ensureUniquePanelLoginUsername(string $base, ?int $exceptUserId = null): string
    {
        $candidate = $base;
        $n = 0;

        while (true) {
            $q = User::query()->where('panel_login_username', $candidate);
            if ($exceptUserId !== null) {
                $q->where('id', '!=', $exceptUserId);
            }
            if (! $q->exists()) {
                return $candidate;
            }
            $n++;
            $candidate = $base.'-'.$n;
        }
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
