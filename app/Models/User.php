<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Administration;
use App\Models\Entity;
use App\Models\Manager;
use App\Models\Seller;
use Illuminate\Support\Facades\Hash;

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

    /**
     * Contraseña por defecto usada históricamente al dar de alta gestores desde el panel.
     * Si el hash coincide, el usuario debe cambiarla al iniciar sesión.
     */
    public const ENTITY_MANAGER_LEGACY_DEFAULT_PASSWORD = '12345678';

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
        'panel_account_type',
        'panel_account_id',
        'panel_login_username',
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
    /** Rol virtual: usuario con registro en tabla managers (gestor en la app). */
    public const ROLE_MANAGER = 'manager';

    /** Cuenta de acceso al panel web vinculada a una administración o entidad. */
    public function isPanelAccount(): bool
    {
        return $this->panel_account_type !== null && $this->panel_account_type !== ''
            && $this->panel_account_id !== null;
    }

    /**
     * Imagen de administración o entidad para el header (cuenta panel).
     */
    public function panelAccountHeaderImageUrl(): ?string
    {
        if (! $this->isPanelAccount() || ! $this->panel_account_id) {
            return null;
        }

        if ($this->panel_account_type === 'administration') {
            $adm = Administration::query()->find($this->panel_account_id);
            if ($adm && $adm->image && is_file(public_path('images/'.$adm->image))) {
                return asset('images/'.$adm->image);
            }

            return null;
        }

        if ($this->panel_account_type === 'entity') {
            $entity = Entity::query()->find($this->panel_account_id);
            if ($entity && $entity->image && is_file(public_path('uploads/'.$entity->image))) {
                return asset('uploads/'.$entity->image);
            }

            return null;
        }

        return null;
    }

    public function scopeWithoutPanelAccount($query)
    {
        return $query->whereNull('panel_account_type');
    }

    public function hasRole(string $role): bool
    {
        // Si no es super admin, ni administración, ni tiene registros como gestor,
        // lo consideramos usuario final (vendedor/cliente) y no debe pasar chequeos
        // de rol dentro de la web.
        if (!$this->isSuperAdmin() && !$this->isAdministration() && !$this->managers()->exists()) {
            return false;
        }

        // Super admin: acceso a todo menos al rol "client"
        if ($this->role === self::ROLE_SUPER_ADMIN && $role !== self::ROLE_CLIENT) {
            return true;
        }

        // Rol virtual "manager": cualquier registro en managers
        if ($role === self::ROLE_MANAGER) {
            return $this->managers()->exists();
        }

        // Administración / entidad basados en managers, dando prioridad a administración
        if ($role === self::ROLE_ADMINISTRATION) {
            return $this->isAdministration();
        }

        if ($role === self::ROLE_ENTITY) {
            return $this->isEntity();
        }

        // Resto de casos: comparamos contra el campo role
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
        if ($this->isSuperAdmin()) {
            return false;
        }
        if ($this->panel_account_type === 'administration') {
            return true;
        }
        if ($this->panel_account_type === 'entity') {
            return false;
        }

        return $this->hasAdministrationManagers() && ! $this->hasEntityManagers();
    }

    /**
     * True si el usuario es gestor de entidad o cuenta panel de entidad.
     */
    public function isEntity(): bool
    {
        if ($this->isSuperAdmin()) {
            return false;
        }
        if ($this->panel_account_type === 'entity') {
            return true;
        }
        if ($this->panel_account_type === 'administration') {
            return false;
        }

        return $this->hasEntityManagers();
    }

    /**
     * Helpers internos para no mezclar lógica de contexto con la consulta base.
     */
    protected function hasAdministrationManagers(): bool
    {
        return $this->managers()
            ->whereNotNull('administration_id')
            ->exists();
    }

    protected function hasEntityManagers(): bool
    {
        return $this->managers()
            ->whereNotNull('entity_id')
            ->exists();
    }

    /** True si el usuario tiene al menos un registro en la tabla sellers (vendedor). */
    public function isSeller(): bool
    {
        return $this->sellers()->exists();
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

            if ($this->panel_account_type === 'administration' && $this->panel_account_id) {
                $administrationIds = collect($administrationIds)
                    ->push((int) $this->panel_account_id)
                    ->unique()
                    ->values()
                    ->all();
            }

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

        if ($this->panel_account_type === 'entity' && $this->panel_account_id) {
            return $this->cachedEntityIds = [(int) $this->panel_account_id];
        }

        if ($this->isAdministration()) {
            $administrationIds = $this->accessibleAdministrationIds();

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
     * Usuarios de app (sin cuenta panel) visibles para un perfil administración:
     * gestores de sus administraciones o entidades, o vendedores ligados a esas entidades.
     * Excluye superadministradores.
     */
    public function scopeForAdministrationScopedViewer(Builder $query, User $viewer): Builder
    {
        if ($viewer->isSuperAdmin() || ! $viewer->isAdministration()) {
            return $query;
        }

        $adminIds = $viewer->accessibleAdministrationIds();
        if ($adminIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where('role', '!=', self::ROLE_SUPER_ADMIN)
            ->where(function (Builder $q) use ($adminIds) {
                $q->whereHas('managers', function (Builder $m) use ($adminIds) {
                    $m->where(function (Builder $inner) use ($adminIds) {
                        $inner->whereIn('administration_id', $adminIds)
                            ->orWhereIn('entity_id', function ($sub) use ($adminIds) {
                                $sub->select('id')
                                    ->from('entities')
                                    ->whereIn('administration_id', $adminIds);
                            });
                    });
                })->orWhereHas('sellers', function (Builder $s) use ($adminIds) {
                    $s->whereHas('entities', function (Builder $e) use ($adminIds) {
                        $e->whereIn('administration_id', $adminIds);
                    });
                });
            });
    }

    /**
     * Si el visor es administración (no superadmin), ¿puede ver/editar este usuario en el panel?
     */
    public function isAccessibleToAdministrationViewer(User $viewer): bool
    {
        if ($viewer->isSuperAdmin() || ! $viewer->isAdministration()) {
            return true;
        }

        return static::query()
            ->whereKey($this->id)
            ->whereNull('panel_account_type')
            ->forAdministrationScopedViewer($viewer)
            ->exists();
    }

    /**
     * Obtener los IDs de vendedores accesibles según el rol del usuario.
     * Incluye a gestores (tabla managers): vendedores de las entidades que gestionan.
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

            if ($this->isEntity() && !$this->isSuperAdmin() && !$this->isAdministration()) {
                $entityIds = $this->accessibleEntityIdsByPermission('sellers');
            }

            if (empty($entityIds)) {
                return $this->cachedSellerIds = $this->mergeOwnSellerIds([]);
            }

            $sellerIds = Seller::query()
                ->whereHas('entities', function ($query) use ($entityIds) {
                    $query->whereIn('entities.id', $entityIds);
                })
                ->pluck('id')
                ->unique()
                ->values()
                ->all();

            return $this->cachedSellerIds = $this->mergeOwnSellerIds($sellerIds);
        }

        // Gestor (tiene registro en managers): puede acceder a vendedores de sus entidades gestionadas
        if ($this->managers()->exists()) {
            $entityIds = $this->getManagerEntityIds();
            if (!empty($entityIds)) {
                $sellerIds = Seller::query()
                    ->whereHas('entities', function ($query) use ($entityIds) {
                        $query->whereIn('entities.id', $entityIds);
                    })
                    ->pluck('id')
                    ->unique()
                    ->values()
                    ->all();
                return $this->cachedSellerIds = $this->mergeOwnSellerIds($sellerIds);
            }
        }

        if ($this->isSeller()) {
            return $this->cachedSellerIds = $this->sellers()->pluck('id')->unique()->values()->all();
        }

        return $this->cachedSellerIds = [];
    }

    /**
     * Añadir los IDs de vendedores propios del usuario a una lista (para gestor que también es vendedor).
     */
    protected function mergeOwnSellerIds(array $sellerIds): array
    {
        $own = $this->sellers()->pluck('id')->all();
        return array_values(array_unique(array_merge($sellerIds, $own)));
    }

    protected function managerPermissionColumn(string $permission): ?string
    {
        return match ($permission) {
            'sellers' => 'permission_sellers',
            'design' => 'permission_design',
            'payments' => 'permission_payments',
            'statistics' => 'permission_statistics',
            default => null,
        };
    }

    public function accessibleEntityIdsByPermission(string $permission): array
    {
        if ($this->isSuperAdmin() || $this->isAdministration()) {
            return $this->accessibleEntityIds();
        }

        $column = $this->managerPermissionColumn($permission);
        if (!$column || !$this->isEntity()) {
            return [];
        }

        return $this->managers()
            ->whereNotNull('entity_id')
            ->where('status', 1)
            ->where($column, true)
            ->whereHas('entity', function ($q) {
                $q->where('status', 1);
            })
            ->pluck('entity_id')
            ->unique()
            ->values()
            ->all();
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

        $hasBaseAccess = in_array($entityId, $this->accessibleEntityIds(), true);
        if (!$hasBaseAccess) {
            return false;
        }

        if ($this->isEntity() && !$this->isAdministration()) {
            if (request()->routeIs('sellers.*')) {
                return in_array($entityId, $this->accessibleEntityIdsByPermission('sellers'), true);
            }
            if (request()->routeIs('design.*')) {
                return in_array($entityId, $this->accessibleEntityIdsByPermission('design'), true);
            }
            if (request()->routeIs('configuration.*') || request()->routeIs('sepa-payments.*')) {
                return in_array($entityId, $this->accessibleEntityIdsByPermission('payments'), true);
            }
        }

        return true;
    }

    /**
     * Determinar si el usuario puede acceder a un vendedor específico.
     * Incluye acceso al propio vendedor cuando el usuario es gestor y vendedor (misma persona).
     */
    public function canAccessSeller(int $sellerId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Si el vendedor es el propio usuario (misma persona como gestor y vendedor), tiene permiso
        if ($this->sellers()->where('id', $sellerId)->exists()) {
            return true;
        }

        return in_array($sellerId, $this->accessibleSellerIds(), true);
    }

    /**
     * True cuando es gestor de entidad (sin cuenta panel propia).
     */
    public function isEntityManagerWithoutPanelAccount(): bool
    {
        if ($this->isSuperAdmin() || $this->isAdministration()) {
            return false;
        }

        return ! $this->isPanelAccount()
            && $this->managers()->whereNotNull('entity_id')->exists();
    }

    /**
     * Permisos de gestor de entidad (aplican solo a managers sin cuenta panel).
     * Si no es gestor de entidad sin panel, devolvemos true para no romper flujos existentes.
     */
    /**
     * Gestor responsable de la entidad con invitación aceptada (manager activo y principal).
     */
    public function isPrimaryAcceptedManagerForEntity(int $entityId): bool
    {
        return $this->managers()
            ->where('entity_id', $entityId)
            ->where('is_primary', true)
            ->where('status', 1)
            ->exists();
    }

    /**
     * Usuario de acceso al panel vinculado a la entidad (supervisión; sin permisos de mutación en web).
     */
    public function isEntityPanelReadOnly(): bool
    {
        return $this->isPanelAccount() && $this->panel_account_type === 'entity';
    }

    /**
     * True si el gestor debe cambiar la contraseña provisional (12345678) antes de usar el panel.
     */
    public function mustChangeEntityManagerLegacyPassword(): bool
    {
        if ($this->isPanelAccount()) {
            return false;
        }

        if ($this->role !== self::ROLE_ENTITY) {
            return false;
        }

        return Hash::check(self::ENTITY_MANAGER_LEGACY_DEFAULT_PASSWORD, $this->password);
    }

    /**
     * Puede tramitar devoluciones/anulaciones para esta entidad (alineado con DevolutionsController).
     */
    public function canManageEntityDevolutions(int $entityId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->isAdministration() && $this->canAccessEntity($entityId)) {
            return true;
        }

        if ($this->isPanelAccount()
            && $this->panel_account_type === 'entity'
            && (int) $this->panel_account_id === $entityId) {
            return false;
        }

        return $this->isPrimaryAcceptedManagerForEntity($entityId);
    }

    /**
     * Acceso al módulo web de devoluciones (menú y pantallas).
     * Incluye cuenta panel de entidad en modo solo consulta; gestores no responsables quedan excluidos.
     */
    public function hasAccessToDevolutionsModule(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->isAdministration()) {
            return true;
        }

        if ($this->isEntityPanelReadOnly()) {
            return true;
        }

        return $this->managers()
            ->whereNotNull('entity_id')
            ->where('is_primary', true)
            ->where('status', 1)
            ->exists();
    }

    /**
     * Puede ver el detalle de devoluciones de la entidad (gestor responsable, administración o panel entidad en consulta).
     */
    public function canViewDevolutionForEntity(int $entityId): bool
    {
        if ($this->canManageEntityDevolutions($entityId)) {
            return true;
        }

        return $this->isEntityPanelReadOnly()
            && $this->panel_account_id
            && (int) $this->panel_account_id === $entityId;
    }

    /**
     * IDs de entidades para las que puede gestionar devoluciones (listados / filtros).
     *
     * @return array<int>|null null = sin filtro (superadmin, todas)
     */
    public function devolutionManagedEntityIds(): ?array
    {
        if ($this->isSuperAdmin()) {
            return null;
        }

        if ($this->isEntityPanelReadOnly() && $this->panel_account_id) {
            return [(int) $this->panel_account_id];
        }

        return array_values(array_filter(
            $this->accessibleEntityIds(),
            fn ($id) => $this->canManageEntityDevolutions((int) $id)
        ));
    }

    public function hasEntityManagerPermission(string $permission)
    {
        $column = $this->managerPermissionColumn($permission);
        if (!$column) {
            return false;
        }

        if ($this->isSuperAdmin() || $this->isAdministration()) {
            return true;
        }

        if (!$this->isEntity()) {
            return false;
        }

        return $this->managers()
            ->whereNotNull('entity_id')
            ->where('status', 1)
            ->where($column, true)
            ->whereHas('entity', function ($q) {
                $q->where('status', 1);
            })
            ->exists();
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
     * IDs de entidades que el usuario gestiona según la tabla managers (no el rol).
     * Usado por la app para el flujo gestor: participaciones de vendedores de sus entidades.
     */
    public function getManagerEntityIds(): array
    {
        $managers = $this->managers()->get();

        // Regla global:
        // - Si existe al menos un manager con entity_id, SOLO usamos esos entity_id.
        // - Si NINGÚN manager tiene entity_id, entonces inferimos por administration_id.
        $hasAnyEntityId = $managers->contains(fn ($m) => !empty($m->entity_id));
        $entityIds = collect();

        if ($hasAnyEntityId) {
            foreach ($managers as $m) {
                if (!empty($m->entity_id)) {
                    $entityIds->push($m->entity_id);
                }
            }
        } else {
            $administrationIds = $managers
                ->pluck('administration_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (!empty($administrationIds)) {
                $entityIds = Entity::whereIn('administration_id', $administrationIds)->pluck('id');
            }
        }

        return $entityIds->unique()->values()->all();
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
