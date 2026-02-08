<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Administration;
use App\Models\Entity;
use App\Models\Manager;
use App\Models\Seller;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected ?array $cachedAdministrationIds = null;
    protected ?array $cachedEntityIds = null;
    protected ?array $cachedSellerIds = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMINISTRATION = 'administration';
    public const ROLE_ENTITY = 'entity';
    public const ROLE_SELLER = 'seller';
    public const ROLE_CLIENT = 'client';

    protected $fillable = [
        'name',
        'last_name',
        'last_name2',
        'nif_cif',
        'birthday',
        'email',
        'phone',
        'comment',
        'image',
        'status',
        'role',
        'password',
        'fcm_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
        'birthday' => 'date',
    ];

    /**
     * Comprobar si el usuario tiene un rol específico.
     */
    public function hasRole(string $role): bool
    {
        if ($this->role === self::ROLE_SUPER_ADMIN && $role !== self::ROLE_CLIENT) {
            return true;
        }

        return $this->role === $role;
    }

    /**
     * Comprobar si el usuario tiene alguno de los roles proporcionados.
     */
    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdministration(): bool
    {
        return $this->role === self::ROLE_ADMINISTRATION;
    }

    public function isEntity(): bool
    {
        return $this->role === self::ROLE_ENTITY;
    }

    public function isSeller(): bool
    {
        return $this->role === self::ROLE_SELLER;
    }

    public function isClient(): bool
    {
        return $this->role === self::ROLE_CLIENT;
    }

    /**
     * Obtener los IDs de administraciones accesibles según el rol del usuario.
     */
    public function accessibleAdministrationIds(): array
    {
        if ($this->cachedAdministrationIds !== null) {
            return $this->cachedAdministrationIds;
        }

        if ($this->isSuperAdmin()) {
            // No necesitamos filtrar para super admin; devolver arreglo vacío indica acceso completo.
            return $this->cachedAdministrationIds = Administration::pluck('id')->all();
        }

        if ($this->isAdministration()) {
            $administrationIds = Manager::query()
                ->where('user_id', $this->id)
                ->whereNotNull('administration_id')
                ->pluck('administration_id')
                ->unique()
                ->values()
                ->all();

            return $this->cachedAdministrationIds = $administrationIds;
        }

        if ($this->isEntity()) {
            $entityIds = $this->accessibleEntityIds();

            if (empty($entityIds)) {
                return $this->cachedAdministrationIds = [];
            }

            $administrationIds = Entity::query()
                ->whereIn('id', $entityIds)
                ->pluck('administration_id')
                ->unique()
                ->values()
                ->all();

            return $this->cachedAdministrationIds = $administrationIds;
        }

        if ($this->isSeller()) {
            $administrationIds = Entity::query()
                ->whereIn('id', $this->accessibleEntityIds())
                ->pluck('administration_id')
                ->unique()
                ->values()
                ->all();

            return $this->cachedAdministrationIds = $administrationIds;
        }

        return $this->cachedAdministrationIds = [];
    }

    /**
     * Obtener los IDs de entidades accesibles según el rol del usuario.
     */
    public function accessibleEntityIds(): array
    {
        if ($this->cachedEntityIds !== null) {
            return $this->cachedEntityIds;
        }

        if ($this->isSuperAdmin()) {
            return $this->cachedEntityIds = Entity::pluck('id')->all();
        }

        if ($this->isAdministration()) {
            $administrationIds = Manager::query()
                ->where('user_id', $this->id)
                ->whereNotNull('administration_id')
                ->pluck('administration_id')
                ->unique()
                ->values()
                ->all();

            if (empty($administrationIds)) {
                return $this->cachedEntityIds = [];
            }

            $entityIds = Entity::query()
                ->whereIn('administration_id', $administrationIds)
                ->pluck('id')
                ->unique()
                ->values()
                ->all();

            return $this->cachedEntityIds = $entityIds;
        }

        if ($this->isEntity()) {
            $entityIds = Manager::query()
                ->where('user_id', $this->id)
                ->whereNotNull('entity_id')
                ->pluck('entity_id')
                ->unique()
                ->values()
                ->all();

            return $this->cachedEntityIds = $entityIds;
        }

        if ($this->isSeller()) {
            $entityIds = $this->sellers()
                ->with('entities:id')
                ->get()
                ->flatMap(fn (Seller $seller) => $seller->entities->pluck('id'))
                ->unique()
                ->values()
                ->all();

            return $this->cachedEntityIds = $entityIds;
        }

        return $this->cachedEntityIds = [];
    }

    /**
     * Obtener los IDs de vendedores accesibles según el rol del usuario.
     */
    public function accessibleSellerIds(): array
    {
        if ($this->cachedSellerIds !== null) {
            return $this->cachedSellerIds;
        }

        if ($this->isSuperAdmin()) {
            return $this->cachedSellerIds = Seller::pluck('id')->all();
        }

        if ($this->isAdministration() || $this->isEntity()) {
            $entityIds = $this->accessibleEntityIds();

            if (empty($entityIds)) {
                return $this->cachedSellerIds = [];
            }

            $sellerIds = Seller::query()
                ->whereHas('entities', function ($query) use ($entityIds) {
                    $query->whereIn('entities.id', $entityIds);
                })
                ->pluck('id')
                ->unique()
                ->values()
                ->all();

            return $this->cachedSellerIds = $sellerIds;
        }

        if ($this->isSeller()) {
            return $this->cachedSellerIds = $this->sellers()->pluck('id')->unique()->values()->all();
        }

        return $this->cachedSellerIds = [];
    }

    /**
     * Determinar si el usuario puede acceder a una administración específica.
     */
    public function canAccessAdministration(int $administrationId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($administrationId, $this->accessibleAdministrationIds(), true);
    }

    /**
     * Determinar si el usuario puede acceder a una entidad específica.
     */
    public function canAccessEntity(int $entityId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($entityId, $this->accessibleEntityIds(), true);
    }

    /**
     * Determinar si el usuario puede acceder a un vendedor específico.
     */
    public function canAccessSeller(int $sellerId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($sellerId, $this->accessibleSellerIds(), true);
    }

    /**
     * Relación con Seller
     */
    public function sellers()
    {
        return $this->hasMany(Seller::class);
    }

    /**
     * Relación con Manager
     */
    public function managers()
    {
        return $this->hasMany(Manager::class);
    }

    /**
     * Obtener el nombre completo del usuario
     */
    public function getFullNameAttribute()
    {
        $fullName = $this->name;
        if ($this->last_name) {
            $fullName .= ' ' . $this->last_name;
        }
        if ($this->last_name2) {
            $fullName .= ' ' . $this->last_name2;
        }
        return $fullName;
    }
}
