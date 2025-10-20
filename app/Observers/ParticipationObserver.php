<?php

namespace App\Observers;

use App\Models\Participation;
use App\Models\ParticipationActivityLog;
use App\Services\FirebaseServiceModern;
use Illuminate\Support\Facades\Log;

class ParticipationObserver
{
    protected $firebaseService;

    public function __construct(FirebaseServiceModern $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    /**
     * Handle the Participation "created" event.
     */
    public function created(Participation $participation): void
    {
        // Registrar la creación de la participación
        ParticipationActivityLog::log($participation->id, 'created', [
            'entity_id' => $participation->entity_id,
            'description' => "Participación #{$participation->participation_number} creada",
            'metadata' => [
                'participation_code' => $participation->participation_code,
                'book_number' => $participation->book_number,
                'set_id' => $participation->set_id,
                'design_format_id' => $participation->design_format_id,
            ],
        ]);
    }

    /**
     * Handle the Participation "updated" event.
     */
    public function updated(Participation $participation): void
    {
        // LOG TEMPORAL: Verificar que el observer se está disparando
        \Log::info("Observer updated() DISPARADO", [
            'participation_id' => $participation->id,
        ]);
        
        // Obtener los cambios realizados
        $changes = $participation->getChanges();
        $original = $participation->getOriginal();

        // LOG TEMPORAL: Ver cambios detectados
        \Log::info("Observer - Cambios detectados", [
            'participation_id' => $participation->id,
            'changes' => $changes,
            'original_seller_id' => $original['seller_id'] ?? null,
            'new_seller_id' => $participation->seller_id,
        ]);

        // Ignorar actualizaciones de timestamps
        unset($changes['updated_at']);

        if (empty($changes)) {
            \Log::info("Observer - No hay cambios significativos después de quitar timestamps");
            return;
        }

        $statusChanged = array_key_exists('status', $changes);
        $sellerChanged = array_key_exists('seller_id', $changes);
        $oldStatus = $original['status'] ?? null;
        $newStatus = $changes['status'] ?? null;
        $oldSellerId = $original['seller_id'] ?? null;
        $newSellerId = $changes['seller_id'] ?? null;
        
        \Log::info("Observer - Variables de control", [
            'statusChanged' => $statusChanged,
            'sellerChanged' => $sellerChanged,
            'oldSellerId' => $oldSellerId,
            'newSellerId' => $newSellerId,
        ]);

        // PRIORIDAD 1: Detectar cuando se QUITA el seller_id (independientemente del estado)
        // Esto debe evaluarse PRIMERO antes que cualquier otro caso
        if ($sellerChanged && $newSellerId === null && $oldSellerId !== null) {
            \Log::info("Observer - CASO DETECTADO: Devolución por vendedor", [
                'participation_id' => $participation->id,
                'old_seller_id' => $oldSellerId,
                'new_seller_id' => $newSellerId,
                'old_status' => $oldStatus,
                'new_status' => $newStatus ?? $oldStatus,
            ]);
            
            ParticipationActivityLog::log($participation->id, 'returned_by_seller', [
                'entity_id' => $participation->entity_id,
                'seller_id' => $oldSellerId,
                'old_seller_id' => $oldSellerId,
                'old_status' => $oldStatus,
                'new_status' => $newStatus ?? $oldStatus, // Usar el estado actual si no cambió
                'description' => "Participación devuelta por el vendedor ID: {$oldSellerId}",
                'metadata' => $changes,
            ]);
            
            // Enviar notificación
            $this->sendNotification($participation, 'returned_by_seller');
            
            \Log::info("Observer - Registro de devolución creado exitosamente");
            return; // Evitar registros duplicados
        }

        // Caso 1: Asignación a vendedor (status cambia a 'asignada' y se asigna vendedor)
        if ($statusChanged && $newStatus === 'asignada' && $sellerChanged && $newSellerId !== null && $oldSellerId === null) {
            ParticipationActivityLog::log($participation->id, 'assigned', [
                'entity_id' => $participation->entity_id,
                'seller_id' => $newSellerId,
                'new_seller_id' => $newSellerId,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'description' => "Participación asignada al vendedor ID: {$newSellerId}",
                'metadata' => $changes,
            ]);
            
            // Enviar notificación
            $this->sendNotification($participation, 'assigned');
            
            return; // Evitar registros duplicados
        }

        // Caso 2: Participación vendida
        if ($statusChanged && $newStatus === 'vendida') {
            ParticipationActivityLog::log($participation->id, 'sold', [
                'entity_id' => $participation->entity_id,
                'seller_id' => $participation->seller_id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'description' => "Participación vendida",
                'metadata' => array_merge($changes, [
                    'sale_amount' => $participation->sale_amount ?? null,
                    'buyer_name' => $participation->buyer_name ?? null,
                ]),
            ]);
            
            // Enviar notificación
            $this->sendNotification($participation, 'sold');
            
            return; // Evitar registros duplicados
        }

        // Caso 4: Devolución a la administración (status cambia a 'devuelta' sin eliminar vendedor)
        if ($statusChanged && $newStatus === 'devuelta' && !($sellerChanged && $newSellerId === null)) {
            ParticipationActivityLog::log($participation->id, 'returned_to_administration', [
                'entity_id' => $participation->entity_id,
                'seller_id' => $participation->seller_id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'description' => "Participación devuelta a la administración",
                'metadata' => array_merge($changes, [
                    'return_reason' => $participation->return_reason ?? null,
                ]),
            ]);
            
            // Enviar notificación
            $this->sendNotification($participation, 'returned_to_administration');
            
            return; // Evitar registros duplicados
        }

        // Caso 5: Participación anulada
        if ($statusChanged && $newStatus === 'anulada') {
            \Log::info("Observer - CASO DETECTADO: Participación anulada", [
                'participation_id' => $participation->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'cancellation_reason' => $participation->cancellation_reason ?? null,
            ]);
            
            ParticipationActivityLog::log($participation->id, 'cancelled', [
                'entity_id' => $participation->entity_id,
                'seller_id' => $participation->seller_id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'description' => "Participación anulada: " . ($participation->cancellation_reason ?? 'Sin motivo especificado'),
                'metadata' => array_merge($changes, [
                    'cancellation_reason' => $participation->cancellation_reason ?? null,
                    'cancelled_by' => $participation->cancelled_by ?? null,
                    'cancellation_date' => $participation->cancellation_date ?? null,
                ]),
            ]);
            
            // Enviar notificación
            $this->sendNotification($participation, 'cancelled');
            
            \Log::info("Observer - Registro de anulación creado exitosamente");
            return; // Evitar registros duplicados
        }

        // Caso 6: Reasignación de vendedor (cambio de un vendedor a otro)
        if ($sellerChanged && $newSellerId !== null && $oldSellerId !== null && $newSellerId !== $oldSellerId) {
            ParticipationActivityLog::log($participation->id, 'assigned', [
                'entity_id' => $participation->entity_id,
                'seller_id' => $newSellerId,
                'old_seller_id' => $oldSellerId,
                'new_seller_id' => $newSellerId,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'description' => "Participación reasignada del vendedor ID: {$oldSellerId} al vendedor ID: {$newSellerId}",
                'metadata' => $changes,
            ]);
            
            // Enviar notificación
            $this->sendNotification($participation, 'reassigned');
            
            return; // Evitar registros duplicados
        }

        // Caso 7: Asignación simple (sin cambio de estado 'asignada')
        if ($sellerChanged && $newSellerId !== null && $oldSellerId === null && (!$statusChanged || $newStatus !== 'asignada')) {
            ParticipationActivityLog::log($participation->id, 'assigned', [
                'entity_id' => $participation->entity_id,
                'seller_id' => $newSellerId,
                'new_seller_id' => $newSellerId,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'description' => "Participación asignada al vendedor ID: {$newSellerId}",
                'metadata' => $changes,
            ]);
            
            // Enviar notificación
            $this->sendNotification($participation, 'assigned');
            
            return; // Evitar registros duplicados
        }

        // Caso 8: Cambio de estado genérico (no cubierto por casos anteriores)
        if ($statusChanged) {
            ParticipationActivityLog::log($participation->id, 'status_changed', [
                'entity_id' => $participation->entity_id,
                'seller_id' => $participation->seller_id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'description' => "Estado cambiado de '{$oldStatus}' a '{$newStatus}'",
                'metadata' => $changes,
            ]);
            return; // Evitar registros duplicados
        }

        // Caso 9: Modificaciones de datos importantes (comprador, importe, etc.)
        $significantFields = ['buyer_name', 'buyer_phone', 'buyer_email', 'buyer_nif', 'sale_amount'];
        $hasSignificantChanges = false;

        foreach ($significantFields as $field) {
            if (isset($changes[$field])) {
                $hasSignificantChanges = true;
                break;
            }
        }

        if ($hasSignificantChanges) {
            ParticipationActivityLog::log($participation->id, 'modified', [
                'entity_id' => $participation->entity_id,
                'seller_id' => $participation->seller_id,
                'description' => "Información de la participación modificada",
                'metadata' => [
                    'changes' => $changes,
                    'original' => array_intersect_key($original, $changes),
                ],
            ]);
        }
    }

    /**
     * Handle the Participation "deleted" event.
     */
    public function deleted(Participation $participation): void
    {
        // Registrar eliminación
        ParticipationActivityLog::log($participation->id, 'cancelled', [
            'entity_id' => $participation->entity_id,
            'seller_id' => $participation->seller_id,
            'description' => "Participación eliminada del sistema",
            'metadata' => [
                'participation_code' => $participation->participation_code,
                'status' => $participation->status,
            ],
        ]);
    }

    /**
     * Handle the Participation "restored" event.
     */
    public function restored(Participation $participation): void
    {
        // Registrar restauración
        ParticipationActivityLog::log($participation->id, 'modified', [
            'entity_id' => $participation->entity_id,
            'seller_id' => $participation->seller_id,
            'description' => "Participación restaurada",
            'metadata' => [
                'participation_code' => $participation->participation_code,
                'status' => $participation->status,
            ],
        ]);
    }

    /**
     * Handle the Participation "force deleted" event.
     */
    public function forceDeleted(Participation $participation): void
    {
        // No registrar nada ya que la participación y sus logs serán eliminados permanentemente
    }

    /**
     * Enviar notificación a los usuarios correctos según el evento
     */
    private function sendNotification($participation, $event, $data = [])
    {
        try {
            $tokensToNotify = $this->getRelevantUserTokens($participation, $event);
            
            if (empty($tokensToNotify)) {
                Log::info("No hay usuarios para notificar sobre el evento '{$event}'");
                return;
            }

            Log::info("📤 Enviando notificación: {$event}", [
                'participation_id' => $participation->id,
                'participation_code' => $participation->participation_code,
                'usuarios_a_notificar' => count($tokensToNotify)
            ]);

            // Preparar título y mensaje según el evento
            $notification = $this->prepareNotificationContent($participation, $event, $data);
            
            // Enviar a cada usuario
            foreach ($tokensToNotify as $userInfo) {
                try {
                    $this->firebaseService->sendToDevice(
                        $userInfo['token'],
                        $notification['title'],
                        $notification['body'],
                        array_merge($notification['data'], [
                            'user_id' => (string)$userInfo['user_id'],
                            'user_role' => $userInfo['role']
                        ])
                    );
                    
                    Log::info("✅ Notificación enviada a {$userInfo['name']} ({$userInfo['role']})");
                } catch (\Exception $e) {
                    Log::error("❌ Error enviando a {$userInfo['name']}: " . $e->getMessage());
                }
            }
            
        } catch (\Exception $e) {
            Log::error("Error general en sendNotification: " . $e->getMessage());
        }
    }

    /**
     * Obtener tokens de usuarios relevantes según el evento
     */
    private function getRelevantUserTokens($participation, $event)
    {
        $tokens = [];
        
        // Cargar relaciones necesarias
        $participation->load(['seller.user', 'entity.manager.user']);
        
        // Obtener manager de la entidad
        if ($participation->entity && $participation->entity->manager && $participation->entity->manager->user) {
            $managerUser = $participation->entity->manager->user;
            if ($managerUser->fcm_token) {
                $tokens[] = [
                    'user_id' => $managerUser->id,
                    'token' => $managerUser->fcm_token,
                    'name' => $managerUser->name,
                    'role' => 'manager'
                ];
            }
        }

        // Según el evento, agregar otros usuarios
        switch ($event) {
            case 'assigned':
            case 'reassigned':
                // Notificar al vendedor asignado
                if ($participation->seller && $participation->seller->user) {
                    $sellerUser = $participation->seller->user;
                    if ($sellerUser->fcm_token) {
                        $tokens[] = [
                            'user_id' => $sellerUser->id,
                            'token' => $sellerUser->fcm_token,
                            'name' => $sellerUser->name,
                            'role' => 'seller'
                        ];
                    }
                }
                break;

            case 'sold':
                // Solo notificar al manager (ya agregado arriba)
                break;

            case 'returned_by_seller':
                // Notificar al vendedor que devolvió
                if ($participation->seller && $participation->seller->user) {
                    $sellerUser = $participation->seller->user;
                    if ($sellerUser->fcm_token) {
                        $tokens[] = [
                            'user_id' => $sellerUser->id,
                            'token' => $sellerUser->fcm_token,
                            'name' => $sellerUser->name,
                            'role' => 'seller'
                        ];
                    }
                }
                break;
        }

        // Eliminar duplicados (por si el manager es también vendedor)
        $uniqueTokens = [];
        $seenUserIds = [];
        
        foreach ($tokens as $tokenInfo) {
            if (!in_array($tokenInfo['user_id'], $seenUserIds)) {
                $uniqueTokens[] = $tokenInfo;
                $seenUserIds[] = $tokenInfo['user_id'];
            }
        }

        return $uniqueTokens;
    }

    /**
     * Preparar contenido de la notificación según el evento
     */
    private function prepareNotificationContent($participation, $event, $data = [])
    {
        $participationCode = $participation->participation_code;
        $sellerName = $data['seller_name'] ?? 'desconocido';
        
        $notifications = [
            'assigned' => [
                'title' => '📋 Participación Asignada',
                'body' => "Se te ha asignado la participación #{$participationCode}",
            ],
            'reassigned' => [
                'title' => '🔄 Participación Reasignada',
                'body' => "La participación #{$participationCode} ha sido reasignada",
            ],
            'sold' => [
                'title' => '✅ Participación Vendida',
                'body' => "La participación #{$participationCode} ha sido vendida",
            ],
            'returned_by_seller' => [
                'title' => '↩️ Participación Devuelta',
                'body' => "La participación #{$participationCode} ha sido devuelta por el vendedor",
            ],
            'returned_to_administration' => [
                'title' => '↩️ Participación Devuelta',
                'body' => "La participación #{$participationCode} ha sido devuelta a la administración",
            ],
            'cancelled' => [
                'title' => '❌ Participación Anulada',
                'body' => "La participación #{$participationCode} ha sido anulada",
            ],
        ];

        $content = $notifications[$event] ?? [
            'title' => '📢 Actualización de Participación',
            'body' => "La participación #{$participationCode} ha sido actualizada",
        ];

        $content['data'] = [
            'type' => 'participation_update',
            'event' => $event,
            'participation_id' => (string)$participation->id,
            'participation_code' => $participationCode,
            'entity_id' => (string)$participation->entity_id,
            'timestamp' => now()->toIso8601String(),
        ];

        return $content;
    }
}
