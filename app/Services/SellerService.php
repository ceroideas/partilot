<?php

namespace App\Services;

use App\Models\User;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SellerService
{
    /**
     * Crear vendedor PARTILOT
     */
    public function createPartilotSeller(array $data, int $entityId): Seller
    {
        return DB::transaction(function () use ($data, $entityId) {
            // Buscar usuario existente por email
            $user = User::where('email', $data['email'])->first();
            
            if ($user) {
                // Usuario existe - crear vendedor vinculado
                $seller = Seller::create([
                    'user_id' => $user->id,
                    'entity_id' => $entityId,
                    'email' => $user->email,
                    'name' => $user->name,
                    'last_name' => $user->last_name,
                    'last_name2' => $user->last_name2,
                    'nif_cif' => $user->nif_cif,
                    'birthday' => $user->birthday,
                    'phone' => $user->phone,
                    'comment' => $data['comment'] ?? null,
                    'image' => $user->image,
                    'status' => $user->status,
                    'seller_type' => 'partilot'
                ]);
                
                Log::info("Vendedor PARTILOT creado y vinculado al usuario {$user->id}");
            } else {
                // Usuario no existe - crear vendedor pendiente de vinculación
                $seller = Seller::create([
                    'user_id' => 0, // Pendiente de vinculación (0 para PARTILOT)
                    'entity_id' => $entityId,
                    'email' => $data['email'],
                    'name' => $data['name'] ?? null, // Puede estar vacío
                    'last_name' => $data['last_name'] ?? null,
                    'last_name2' => $data['last_name2'] ?? null,
                    'nif_cif' => $data['nif_cif'] ?? null,
                    'birthday' => $data['birthday'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'comment' => $data['comment'] ?? null,
                    'image' => null,
                    'status' => true,
                    'seller_type' => 'partilot'
                ]);
                
                Log::info("Vendedor PARTILOT creado pendiente de vinculación con email: {$data['email']}");
            }
            
            return $seller;
        });
    }

    /**
     * Crear vendedor EXTERNO
     */
    public function createExternalSeller(array $data, int $entityId): Seller
    {
        return Seller::create([
            'user_id' => 0, // Sin usuario
            'entity_id' => $entityId,
            'email' => $data['email'],
            'name' => $data['name'] ?? null, // Puede estar vacío
            'last_name' => $data['last_name'] ?? null,
            'last_name2' => $data['last_name2'] ?? null,
            'nif_cif' => $data['nif_cif'] ?? null,
            'birthday' => $data['birthday'] ?? null,
            'phone' => $data['phone'] ?? null,
            'comment' => $data['comment'] ?? null,
            'image' => $data['image'] ?? null,
            'status' => $data['status'] ?? true,
            'seller_type' => 'externo'
        ]);
    }

    /**
     * Crear vendedor según el tipo
     */
    public function createSeller(array $data, int $entityId, string $sellerType): Seller
    {
        switch ($sellerType) {
            case 'partilot':
                return $this->createPartilotSeller($data, $entityId);
            case 'externo':
                return $this->createExternalSeller($data, $entityId);
            default:
                throw new \InvalidArgumentException("Tipo de vendedor no válido: {$sellerType}");
        }
    }

    /**
     * Obtener vendedores con información de vinculación
     */
    public function getSellersWithLinkInfo(int $entityId = null)
    {
        $query = Seller::with('user', 'entity');
        
        if ($entityId) {
            $query->where('entity_id', $entityId);
        }
        
        return $query->get()->map(function ($seller) {
            return [
                'id' => $seller->id,
                'name' => $seller->display_name,
                'email' => $seller->display_email,
                'phone' => $seller->display_phone,
                'entity_name' => $seller->entity->name ?? 'N/A',
                'status' => $seller->status_text,
                'status_class' => $seller->status_class,
                'seller_type' => $seller->seller_type,
                'link_status' => $this->getLinkStatus($seller),
                'user_id' => $seller->user_id,
                'created_at' => $seller->created_at->format('d/m/Y H:i')
            ];
        });
    }

    /**
     * Obtener estado de vinculación del vendedor
     */
    private function getLinkStatus(Seller $seller): string
    {
        if ($seller->seller_type === 'externo') {
            return 'Externo';
        }
        
        if ($seller->isLinkedToUser()) {
            return 'Vinculado';
        }
        
        if ($seller->isPendingLink()) {
            return 'Pendiente';
        }
        
        return 'Sin vincular';
    }
}
