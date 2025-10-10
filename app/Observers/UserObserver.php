<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Seller;
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
    }
}
