<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Seller;
use App\Models\Manager;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Buscar vendedores pendientes de vinculación con el mismo email
        // Incluye tanto PARTILOT pendientes como EXTERNO
        $pendingSellers = Seller::where('email', $user->email)
            ->where('user_id', 0) // Vendedores con user_id = 0 (pendientes o externos)
            ->get();

        if ($pendingSellers->isNotEmpty()) {
            // Si hay vendedores pendientes, asignar rol de seller si el usuario no tiene un rol específico
            // (si es super_admin, client por defecto, o no tiene rol específico)
            if (!$user->role || $user->role === User::ROLE_SUPER_ADMIN || $user->role === User::ROLE_CLIENT) {
                $user->update(['role' => User::ROLE_SELLER]);
                Log::info("Rol de seller asignado al usuario {$user->id} debido a vendedores pendientes");
            }
        }

        foreach ($pendingSellers as $seller) {
            try {
                // Vincular el vendedor al usuario
                $seller->update([
                    'user_id' => $user->id,
                    // Sincronizar datos del usuario al vendedor
                    'name' => $user->name,
                    'last_name' => $user->last_name ?? null,
                    'last_name2' => $user->last_name2 ?? null,
                    'nif_cif' => $user->nif_cif ?? null,
                    'birthday' => $user->birthday ?? null,
                    'phone' => $user->phone ?? null,
                ]);

                Log::info("Vendedor {$seller->id} vinculado automáticamente al usuario {$user->id}");
            } catch (\Exception $e) {
                Log::error("Error al vincular vendedor {$seller->id} al usuario {$user->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Verificar si el status cambió
        $statusChanged = $user->wasChanged('status');
        
        // Sincronizar cambios del usuario a sus vendedores vinculados
        $linkedSellers = Seller::where('user_id', $user->id)->get();

        foreach ($linkedSellers as $seller) {
            try {
                $seller->update([
                    'name' => $user->name,
                    'last_name' => $user->last_name ?? null,
                    'last_name2' => $user->last_name2 ?? null,
                    'nif_cif' => $user->nif_cif ?? null,
                    'birthday' => $user->birthday ?? null,
                    'phone' => $user->phone ?? null,
                ]);

                Log::info("Datos del vendedor {$seller->id} sincronizados con usuario {$user->id}");
            } catch (\Exception $e) {
                Log::error("Error al sincronizar vendedor {$seller->id} con usuario {$user->id}: " . $e->getMessage());
            }
        }

        // Si el status cambió, sincronizar con los managers vinculados
        if ($statusChanged) {
            $linkedManagers = Manager::where('user_id', $user->id)->get();

            // Convertir el status boolean del User a integer para Manager
            // true (1) -> 1 (activo), false (0) -> 0 (inactivo)
            $managerStatus = $user->status ? 1 : 0;

            foreach ($linkedManagers as $manager) {
                try {
                    $manager->update([
                        'status' => $managerStatus,
                    ]);

                    Log::info("Status del manager {$manager->id} sincronizado con usuario {$user->id} (status: {$managerStatus})");
                } catch (\Exception $e) {
                    Log::error("Error al sincronizar status del manager {$manager->id} con usuario {$user->id}: " . $e->getMessage());
                }
            }
        }
    }
}
