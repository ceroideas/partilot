<?php

namespace App\Services;

use App\Models\User;
use App\Models\Seller;
use App\Mail\SellerConfirmationMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SellerService
{
    /**
     * Crear vendedor PARTILOT
     */
    public function createPartilotSeller(array $data, int $entityId): Seller
    {
        return DB::transaction(function () use ($data, $entityId) {
            // Primero verificar si ya existe un seller con este email
            $existingSeller = Seller::with('entities')->where('email', $data['email'])->first();
            
            if ($existingSeller) {
                // Seller ya existe - verificar si ya está vinculado a esta entidad
                if ($existingSeller->entities->contains($entityId)) {
                    throw new \Exception("Este vendedor ya está asignado a la entidad seleccionada");
                }
                
                // Agregar la nueva entidad al seller existente
                $existingSeller->entities()->attach($entityId);
                
                Log::info("Vendedor existente ID:{$existingSeller->id} agregado a la entidad {$entityId}");
                return $existingSeller;
            }
            
            // No existe seller con este email, buscar usuario
            $user = User::where('email', $data['email'])->first();
            
            // Generar token de confirmación
            $confirmationToken = Str::random(64);
            
            if ($user) {
                // Usuario existe - crear vendedor pendiente de confirmación
                $seller = Seller::create([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'last_name' => $user->last_name,
                    'last_name2' => $user->last_name2,
                    'nif_cif' => $user->nif_cif,
                    'birthday' => $user->birthday,
                    'phone' => $user->phone,
                    'comment' => $data['comment'] ?? null,
                    'image' => $user->image,
                    'status' => Seller::STATUS_PENDING, // Siempre pendiente hasta confirmación
                    'seller_type' => 'partilot',
                    'confirmation_token' => $confirmationToken,
                    'confirmation_sent_at' => now()
                ]);
                
                // Vincular con la entidad (tabla pivote)
                $seller->entities()->attach($entityId);

                if ($user->role !== User::ROLE_SELLER) {
                    $user->update(['role' => User::ROLE_SELLER]);
                }
                
                Log::info("Vendedor PARTILOT creado pendiente de confirmación, usuario {$user->id} y entidad {$entityId}");
            } else {
                // Usuario no existe - crear vendedor pendiente de vinculación y confirmación
                $seller = Seller::create([
                    'user_id' => 0, // Pendiente de vinculación (0 para PARTILOT)
                    'email' => $data['email'],
                    'name' => $data['name'] ?? null, // Puede estar vacío
                    'last_name' => $data['last_name'] ?? null,
                    'last_name2' => $data['last_name2'] ?? null,
                    'nif_cif' => $data['nif_cif'] ?? null,
                    'birthday' => $data['birthday'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'comment' => $data['comment'] ?? null,
                    'image' => null,
                    'status' => Seller::STATUS_PENDING, // Pendiente hasta aceptación
                    'seller_type' => 'partilot',
                    'confirmation_token' => $confirmationToken,
                    'confirmation_sent_at' => now()
                ]);
                
                // Vincular con la entidad (tabla pivote)
                $seller->entities()->attach($entityId);
                
                Log::info("Vendedor PARTILOT creado pendiente de vinculación y confirmación con email: {$data['email']} y entidad {$entityId}");
            }
            
            // Enviar correo de confirmación
            try {
                Mail::to($seller->email)->send(new SellerConfirmationMail($seller));
                Log::info("Correo de confirmación enviado a {$seller->email}");
            } catch (\Exception $e) {
                Log::error("Error al enviar correo de confirmación: " . $e->getMessage());
                // No lanzar excepción, el vendedor ya está creado
            }
            
            return $seller;
        });
    }

    /**
     * Crear vendedor EXTERNO
     */
    public function createExternalSeller(array $data, int $entityId): Seller
    {
        return DB::transaction(function () use ($data, $entityId) {
            // Primero verificar si ya existe un seller con este email
            $existingSeller = Seller::with('entities')->where('email', $data['email'])->first();
            
            if ($existingSeller) {
                // Seller ya existe - verificar si ya está vinculado a esta entidad
                if ($existingSeller->entities->contains($entityId)) {
                    throw new \Exception("Este vendedor ya está asignado a la entidad seleccionada");
                }
                
                // Agregar la nueva entidad al seller existente
                $existingSeller->entities()->attach($entityId);
                
                Log::info("Vendedor EXTERNO existente ID:{$existingSeller->id} agregado a la entidad {$entityId}");
                return $existingSeller;
            }
            
            // Generar token de confirmación
            $confirmationToken = Str::random(64);
            
            // No existe, crear nuevo vendedor externo pendiente de confirmación
            $seller = Seller::create([
                'user_id' => 0, // Sin usuario
                'email' => $data['email'],
                'name' => $data['name'] ?? null, // Puede estar vacío
                'last_name' => $data['last_name'] ?? null,
                'last_name2' => $data['last_name2'] ?? null,
                'nif_cif' => $data['nif_cif'] ?? null,
                'birthday' => $data['birthday'] ?? null,
                'phone' => $data['phone'] ?? null,
                'comment' => $data['comment'] ?? null,
                'image' => $data['image'] ?? null,
                'status' => Seller::STATUS_PENDING, // Siempre pendiente hasta confirmación
                'seller_type' => 'externo',
                'confirmation_token' => $confirmationToken,
                'confirmation_sent_at' => now()
            ]);
            
            // Vincular con la entidad (tabla pivote)
            $seller->entities()->attach($entityId);
            
            Log::info("Vendedor EXTERNO creado pendiente de confirmación y vinculado a la entidad {$entityId}");
            
            // Enviar correo de confirmación
            try {
                Mail::to($seller->email)->send(new SellerConfirmationMail($seller));
                Log::info("Correo de confirmación enviado a {$seller->email}");
            } catch (\Exception $e) {
                Log::error("Error al enviar correo de confirmación: " . $e->getMessage());
                // No lanzar excepción, el vendedor ya está creado
            }
            
            return $seller;
        });
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
        $query = Seller::with('user', 'entities');
        
        if ($entityId) {
            $query->whereHas('entities', fn($q) => $q->where('entities.id', $entityId));
        }
        
        return $query->get()->map(function ($seller) {
            return [
                'id' => $seller->id,
                'name' => $seller->display_name,
                'email' => $seller->display_email,
                'phone' => $seller->display_phone,
                'entity_name' => $seller->entities->pluck('name')->join(', ') ?: 'N/A',
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
