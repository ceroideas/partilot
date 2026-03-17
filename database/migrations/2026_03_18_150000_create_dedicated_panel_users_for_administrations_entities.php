<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Administration;
use App\Models\Entity;
use App\Models\Manager;
use App\Models\User;

/**
 * Una cuenta de panel por administración y por entidad: usuario con los datos del registro
 * (email de la administración/entidad, nombre comercial/sociedad, NIF, teléfono, etc.),
 * más un Manager para mantener permisos. Los gestores antiguos dejan de tener panel_account_*.
 */
return new class extends Migration
{
    private const DEFAULT_PANEL_PASSWORD = 'PanelMigracion2026!';

    public function up(): void
    {
        DB::table('users')->update([
            'panel_account_type' => null,
            'panel_account_id' => null,
        ]);

        foreach (Administration::query()->orderBy('id')->cursor() as $adm) {
            $this->ensureAdministrationPanelUser($adm);
        }

        foreach (Entity::query()->orderBy('id')->cursor() as $entity) {
            $this->ensureEntityPanelUser($entity);
        }
    }

    private function ensureAdministrationPanelUser(Administration $adm): void
    {
        $email = trim((string) $adm->email);
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $displayName = Administration::panelDisplayNameFromParts($adm->name, $adm->society);

        $user = User::where('email', $email)->first();

        if ($user) {
            if ($user->isPanelAccount()) {
                if ($user->panel_account_type !== 'administration' || (int) $user->panel_account_id !== (int) $adm->id) {
                    return;
                }
            } else {
                $isGestorOfThisAdmin = Manager::query()
                    ->where('user_id', $user->id)
                    ->where('administration_id', $adm->id)
                    ->whereNull('entity_id')
                    ->exists();
                if (! $isGestorOfThisAdmin) {
                    return;
                }
            }

            $user->update([
                'name' => $displayName,
                'nif_cif' => $adm->nif_cif,
                'phone' => $adm->phone,
                'role' => User::ROLE_ADMINISTRATION,
                'panel_account_type' => 'administration',
                'panel_account_id' => $adm->id,
                'status' => true,
            ]);
        } else {
            $user = User::create([
                'name' => $displayName,
                'email' => $email,
                'password' => Hash::make(self::DEFAULT_PANEL_PASSWORD),
                'nif_cif' => $adm->nif_cif,
                'phone' => $adm->phone,
                'role' => User::ROLE_ADMINISTRATION,
                'panel_account_type' => 'administration',
                'panel_account_id' => $adm->id,
                'status' => true,
            ]);
        }

        Manager::query()
            ->where('administration_id', $adm->id)
            ->whereNull('entity_id')
            ->update(['is_primary' => false]);

        Manager::updateOrCreate(
            [
                'user_id' => $user->id,
                'administration_id' => $adm->id,
                'entity_id' => null,
            ],
            [
                'is_primary' => true,
                'permission_sellers' => true,
                'permission_design' => true,
                'permission_statistics' => true,
                'permission_payments' => true,
                'status' => 1,
            ]
        );
    }

    private function ensureEntityPanelUser(Entity $entity): void
    {
        $email = trim((string) $entity->email);
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $displayName = trim((string) $entity->name) ?: 'Entidad';

        $user = User::where('email', $email)->first();

        if ($user) {
            if ($user->panel_account_type === 'administration') {
                return;
            }
            if ($user->isPanelAccount() && $user->panel_account_type === 'entity' && (int) $user->panel_account_id !== (int) $entity->id) {
                return;
            }
            if (! $user->isPanelAccount()) {
                $isGestorOfThisEntity = Manager::query()
                    ->where('user_id', $user->id)
                    ->where('entity_id', $entity->id)
                    ->exists();
                if (! $isGestorOfThisEntity) {
                    return;
                }
            }

            $user->update([
                'name' => $displayName,
                'nif_cif' => $entity->nif_cif,
                'phone' => $entity->phone,
                'role' => User::ROLE_ENTITY,
                'panel_account_type' => 'entity',
                'panel_account_id' => $entity->id,
                'status' => true,
            ]);
        } else {
            $user = User::create([
                'name' => $displayName,
                'email' => $email,
                'password' => Hash::make(self::DEFAULT_PANEL_PASSWORD),
                'nif_cif' => $entity->nif_cif,
                'phone' => $entity->phone,
                'role' => User::ROLE_ENTITY,
                'panel_account_type' => 'entity',
                'panel_account_id' => $entity->id,
                'status' => true,
            ]);
        }

        Manager::query()
            ->where('entity_id', $entity->id)
            ->update(['is_primary' => false]);

        Manager::updateOrCreate(
            [
                'user_id' => $user->id,
                'entity_id' => $entity->id,
            ],
            [
                'administration_id' => null,
                'is_primary' => true,
                'permission_sellers' => true,
                'permission_design' => true,
                'permission_statistics' => true,
                'permission_payments' => true,
                'status' => 1,
            ]
        );
    }

    public function down(): void
    {
        // No revertir: eliminaría usuarios creados y rompería accesos.
    }
};
